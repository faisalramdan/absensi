<?php

namespace App\Http\Controllers;

use App\Models\ShiftAssignment;
use App\Models\Shift;
use App\Models\Employee;
use App\Models\Holiday; // 1. IMPORT MODEL HOLIDAY DI SINI
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;
use App\Models\User;

class ShiftAssignmentController extends Controller
{
    /**
     * Menampilkan Daftar Penjadwalan Karyawan
     */
    public function index(Request $request)
    {
        // Tangkap filter bulan & tahun. Jika kosong, default ke bulan & tahun sekarang
        $chosenMonth = $request->input('month', date('m'));
        $chosenYear = $request->input('year', date('Y'));

        // Jika pilih Juni, maka Start = 26 Mei, End = 25 Juni
        $startDate = Carbon::create($chosenYear, $chosenMonth, 26)->subMonth();
        $endDate = Carbon::create($chosenYear, $chosenMonth, 25);

        // Generate semua tanggal di dalam periode untuk kolom tabel
        $period = CarbonPeriod::create($startDate, $endDate);
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        $shifts = Shift::all();

        // --- TAMBAHAN: Ambil data master hari libur di periode cut-off ini ---
        $holidays = Holiday::whereBetween('date_applied', [
            $startDate->startOfDay()->toDateTimeString(),
            $endDate->endOfDay()->toDateTimeString()
        ])
            ->pluck('name', 'date_applied') // Hasilnya: ['2026-06-01' => 'Hari Lahir Pancasila']
            ->toArray();

        // Ambil data karyawan dan FILTER HANYA YANG AKTIF (`is_active` = true)
        $search = $request->input('search');
        $employees = Employee::where('is_active', true) // 🌟 Perubahan di sini: Hanya memunculkan karyawan aktif
            ->when($search, function ($query) use ($search) {
                $query->where('full_name', 'like', '%' . $search . '%');
            })
            ->orderBy('full_name', 'asc')
            ->get();

        // Ambil data penugasan berdasarkan rentang tanggal dinamis
        $assignments = ShiftAssignment::with('shift')
            ->whereBetween('date', [
                $startDate->startOfDay()->toDateTimeString(),
                $endDate->endOfDay()->toDateTimeString()
            ])
            ->get();

        // Susun matriks berdasarkan employee_id
        $assignmentsData = [];
        foreach ($assignments as $assignment) {
            $formattedDate = Carbon::parse($assignment->date)->format('Y-m-d');
            $empId = $assignment->employee_id;

            if ($empId) {
                $assignmentsData[$empId][$formattedDate] = $assignment->shift->name ?? '-';
            }
        }

        // Tambahkan 'holidays' ke dalam compact
        return view('assignments.index', compact('employees', 'dates', 'shifts', 'assignmentsData', 'startDate', 'endDate', 'holidays'));
    }

    /**
     * Tampilan Form Buat Jadwal Massal
     */
    public function create()
    {
        $shifts = Shift::orderBy('name', 'asc')->get();
        $employees = Employee::where('is_active', true)->orderBy('full_name', 'asc')->get();

        return view('assignments.create', compact('shifts', 'employees'));
    }

    /**
     * Menyimpan Jadwal Massal ke Database
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'shift_id' => 'required|exists:shifts,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string|max:255',
        ]);

        $createdBy = Auth::user()->employee?->id ?? null;

        $period = CarbonPeriod::create($request->start_date, $request->end_date);
        $shift = Shift::findOrFail($request->shift_id);

        // 2. AMBIL SEMUA TANGGAL LIBUR YANG BERADA DI DALAM RENTANG TANGGAL INPUT
        $holidays = Holiday::whereBetween('date_applied', [$request->start_date, $request->end_date])
            ->pluck('name', 'date_applied') // Mengambil array [ 'YYYY-MM-DD' => 'Nama Libur' ]
            ->toArray();

        try {
            DB::beginTransaction();

            foreach ($request->employee_ids as $employeeId) {
                foreach ($period as $date) {
                    $formattedDate = $date->format('Y-m-d');

                    // KUNCI UTAMA 1: Jika hari tersebut adalah hari Minggu, langsung lewati
                    if ($date->isSunday()) {
                        continue;
                    }

                    // KUNCI UTAMA 2: SAMBUNGAN MASTER HARI LIBUR
                    // Jika tanggal ini terdaftar di tabel master holiday (date_applied), otomatis dilewati
                    if (array_key_exists($formattedDate, $holidays)) {
                        continue;
                    }

                    ShiftAssignment::updateOrCreate(
                        [
                            'employee_id' => $employeeId,
                            'date' => $formattedDate,
                        ],
                        [
                            'shift_id' => $request->shift_id,
                            'notes' => $request->notes,
                            'created_by' => $createdBy,
                        ]
                    );
                }
            }

            // --- PERBAIKAN LOG AKTIVITAS (Menambahkan info Libur Nasional) ---
            ActivityLogger::log(
                'ShiftAssignment',
                'Create',
                'Menambahkan jadwal massal shift "' . $shift->name . '" untuk ' . count($request->employee_ids) . ' karyawan. (Hari Minggu & Hari Libur Nasional otomatis dilewati)',
                [],
                [
                    'shift_name' => $shift->name,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'total_employees' => count($request->employee_ids),
                    'employee_ids' => $request->employee_ids,
                    'holidays_skipped' => $holidays, // Mencatat hari libur apa saja yang dilewati pada log
                    'notes' => $request->notes
                ]
            );
            // --- END LOG ---

            DB::commit();
            return redirect()->route('assignments.index')->with('success', 'Penjadwalan massal karyawan berhasil disimpan! Hari Minggu dan Hari Libur Nasional otomatis dilewati.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan penjadwalan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus Jadwal Karyawan
     */
    public function destroy(ShiftAssignment $assignment)
    {
        try {
            $assignment->delete();
            return redirect()->route('assignments.index')->with('success', 'Jadwal karyawan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('assignments.index')->with('error', 'Gagal menghapus jadwal.');
        }
    }

    public function getAvailableEmployees(Request $request)
    {
        try {
            $month = $request->input('month', date('m'));
            $year = $request->input('year', date('Y'));

            $endDateString = "{$year}-{$month}-25 23:59:59";
            $startTime = strtotime("-1 month", strtotime("{$year}-{$month}-26"));
            $startDateString = date('Y-m-d 00:00:00', $startTime);

            // 1. Ambil ID karyawan yang sudah punya jadwal di periode ini
            $bookedEmployeeIds = \DB::table('shift_assignments')
                ->whereBetween('date', [$startDateString, $endDateString])
                ->pluck('employee_id')
                ->unique()
                ->toArray();

            // 2. Query tabel employees dan FILTER HANYA YANG AKTIF (`is_active` = true)
            $query = \DB::table('employees')
                ->where('is_active', true) // 🌟 Perubahan di sini: Hanya karyawan aktif
                ->orderBy('full_name', 'asc');

            // 3. Singkirkan karyawan yang sudah punya jadwal
            if (!empty($bookedEmployeeIds)) {
                $query->whereNotIn('id', $bookedEmployeeIds);
            }

            // 4. Ambil data hasil filter
            $availableEmployees = $query->get(['id', 'full_name', 'nik']);

            return response()->json($availableEmployees);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}