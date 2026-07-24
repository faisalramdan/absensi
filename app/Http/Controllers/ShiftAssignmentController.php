<?php

namespace App\Http\Controllers;

use App\Models\ShiftAssignment;
use App\Models\Shift;
use App\Models\Employee;
use App\Models\Company;
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
        $companies = Company::all();

        // Tangkap filter bulan & tahun. Jika kosong, default ke bulan & tahun sekarang
        $chosenMonth = $request->input('month', date('m'));
        $chosenYear = $request->input('year', date('Y'));

        // Periode cut-off (26 Bulan Lalu s/d 25 Bulan Ini)
        $startDate = Carbon::create($chosenYear, $chosenMonth, 26)->subMonth();
        $endDate = Carbon::create($chosenYear, $chosenMonth, 25);

        // Generate semua tanggal di dalam periode untuk kolom tabel
        $period = CarbonPeriod::create($startDate, $endDate);
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        $shifts = Shift::all();

        // Ambil data master hari libur
        $holidays = Holiday::whereBetween('date_applied', [
            $startDate->startOfDay()->toDateTimeString(),
            $endDate->endOfDay()->toDateTimeString()
        ])
            ->pluck('name', 'date_applied')
            ->toArray();

        // 🌟 LOGIKA BARU: Tentukan Perusahaan yang sedang terpilih (Sama dengan logika Blade)
        $selectedCompanyId = $request->input('company_id');
        if (!$request->has('company_id')) {
            // Jika baru pertama kali buka halaman (belum klik filter), samakan dengan company user login
            $selectedCompanyId = auth()->user()->company_id ?? (auth()->user()->employee->company_id ?? null);
        }

        // 🌟 1. Ambil SEMUA karyawan aktif untuk kebutuhan list Dropdown di View (agar JS bisa memfilter)
        $allActiveEmployees = Employee::where('is_active', true)
            ->orderBy('full_name', 'asc')
            ->get();

        // 🌟 2. Filter karyawan yang akan TAMPIL di tabel matriks
        $selectedEmployeeId = $request->input('employee_id'); // Mengambil id dari dropdown

        $employees = Employee::where('is_active', true)
            // Tambahan: Filter berdasarkan Perusahaan
            ->when($selectedCompanyId, function ($query) use ($selectedCompanyId) {
                $query->where('company_id', $selectedCompanyId);
            })
            // Filter berdasarkan Karyawan Spesifik (jika dipilih)
            ->when($selectedEmployeeId, function ($query) use ($selectedEmployeeId) {
                $query->where('id', $selectedEmployeeId);
            })
            ->orderBy('full_name', 'asc')
            ->get();

        // Ambil ID Karyawan yang lolos filter untuk membatasi kueri assignments
        $employeeIds = $employees->pluck('id')->toArray();

        // Saring data penugasan hanya untuk karyawan yang sedang tampil
        $assignments = ShiftAssignment::with('shift')
            ->whereIn('employee_id', $employeeIds)
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
                $assignmentsData[$empId][$formattedDate] = [
                    'shift_id' => $assignment->shift_id,
                    'shift_name' => $assignment->shift->name ?? '-'
                ];
            }
        }

        // 🌟 Kirim variabel ke View
        return view('assignments.index', compact(
            'companies',
            'employees',
            'allActiveEmployees',
            'dates',
            'shifts',
            'assignmentsData',
            'startDate',
            'endDate',
            'holidays'
        ));
    }

    /**
     * Tampilan Form Buat Jadwal Massal
     */
    public function create()
    {
        $companies = Company::all();
        $shifts = Shift::orderBy('name', 'asc')->get();
        $employees = Employee::where('is_active', true)->orderBy('full_name', 'asc')->get();

        return view('assignments.create', compact('companies', 'shifts', 'employees'));
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
            // Tambahkan validasi untuk settingan baru (opsional, default ke boolean)
            'include_sunday' => 'nullable|boolean',
            'include_holiday' => 'nullable|boolean',
        ]);

        $createdBy = Auth::user()->employee?->id ?? null;
        $period = CarbonPeriod::create($request->start_date, $request->end_date);
        $shift = Shift::findOrFail($request->shift_id);

        // Ambil data libur HANYA jika user memilih untuk MELEWATI hari libur (include_holiday tidak dicentang)
        $holidays = [];
        if (!$request->has('include_holiday')) {
            $holidays = Holiday::whereBetween('date_applied', [$request->start_date, $request->end_date])
                ->pluck('name', 'date_applied')
                ->toArray();
        }

        try {
            DB::beginTransaction();

            $upsertData = [];

            foreach ($request->employee_ids as $employeeId) {
                foreach ($period as $date) {
                    $formattedDate = $date->format('Y-m-d');

                    // KONDISI DINAMIS 1: Jika HARI MINGGU dan user TIDAK mencentang "include_sunday", maka SKIP
                    if ($date->isSunday() && !$request->has('include_sunday')) {
                        continue;
                    }

                    // KONDISI DINAMIS 2: Jika HARI LIBUR dan user TIDAK mencentang "include_holiday", maka SKIP
                    if (!$request->has('include_holiday') && array_key_exists($formattedDate, $holidays)) {
                        continue;
                    }

                    $upsertData[] = [
                        'employee_id' => $employeeId,
                        'date' => $formattedDate,
                        'shift_id' => $request->shift_id,
                        'notes' => $request->notes,
                        'created_by' => $createdBy,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($upsertData)) {
                ShiftAssignment::upsert(
                    $upsertData,
                    ['employee_id', 'date'],
                    ['shift_id', 'notes', 'created_by', 'updated_at']
                );
            }

            // Catat di log sesuai dengan settingan yang dipilih user
            ActivityLogger::log(
                'ShiftAssignment',
                'Create',
                'Menambahkan jadwal massal shift "' . $shift->name . '" dengan opsi dinamis (Minggu: ' . ($request->has('include_sunday') ? 'Masuk' : 'Lewati') . ', Libur: ' . ($request->has('include_holiday') ? 'Masuk' : 'Lewati') . ').',
                [],
                $request->all()
            );

            DB::commit();
            return redirect()->route('assignments.index')->with('success', 'Penjadwalan massal berhasil disimpan sesuai pengaturan yang dipilih!');

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

            // 🌟 1. Tangkap parameter company_id dari AJAX
            $companyId = $request->input('company_id');

            $endDateString = "{$year}-{$month}-25 23:59:59";
            $startTime = strtotime("-1 month", strtotime("{$year}-{$month}-26"));
            $startDateString = date('Y-m-d 00:00:00', $startTime);

            // 2. Ambil ID karyawan yang sudah punya jadwal di periode ini
            $bookedEmployeeIds = \DB::table('shift_assignments')
                ->whereBetween('date', [$startDateString, $endDateString])
                ->pluck('employee_id')
                ->unique()
                ->toArray();

            // 3. Query tabel employees dan FILTER HANYA YANG AKTIF (`is_active` = true)
            $query = \DB::table('employees')
                ->where('is_active', true)
                ->orderBy('full_name', 'asc');

            // 🌟 2. LOGIKA BARU: Filter berdasarkan Perusahaan jika perusahaan terpilih
            if (!empty($companyId)) {
                $query->where('company_id', $companyId);
            }

            // 4. Singkirkan karyawan yang sudah punya jadwal
            if (!empty($bookedEmployeeIds)) {
                $query->whereNotIn('id', $bookedEmployeeIds);
            }

            // 5. Ambil data hasil filter
            $availableEmployees = $query->get(['id', 'full_name', 'nik']);

            return response()->json($availableEmployees);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * 🌟 METHOD BARU: Menyimpan perubahan live edit dari AJAX matriks (perubahan shift)
     */
    public function updateInline(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'shift_id' => 'nullable|exists:shifts,id', // Nullable jika direset ke "-"
        ]);

        $createdBy = Auth::user()->employee?->id ?? null;

        try {
            DB::beginTransaction();

            // Skenario 1: Jika user memilih opsi "-" (Kosong), hapus baris jadwal tersebut
            if (empty($request->shift_id)) {
                ShiftAssignment::where('employee_id', $request->employee_id)
                    ->where('date', $request->date)
                    ->delete();

                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Jadwal berhasil dihapus'
                ]);
            }

            // Skenario 2: Jika memilih shift tertentu, simpan atau update data (Upsert)
            ShiftAssignment::updateOrCreate(
                [
                    'employee_id' => $request->employee_id,
                    'date' => $request->date,
                ],
                [
                    'shift_id' => $request->shift_id,
                    'created_by' => $createdBy,
                    'updated_at' => now(),
                ]
            );

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Jadwal berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengubah jadwal: ' . $e->getMessage()
            ], 500);
        }
    }
}