<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Holiday;


class AttendanceMonthlyController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil semua karyawan aktif untuk dropdown filter di view
        $employees = Employee::where('is_active', true)
            ->orderBy('full_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Periode Default (Cut-off 26 - 25)
        |--------------------------------------------------------------------------
        */
        $selectedYear = $request->input('year', now()->year);
        $selectedMonth = $request->input('month', now()->format('m'));

        // Membuat acuan tanggal awal bulan terpilih
        $target = Carbon::create($selectedYear, $selectedMonth, 1);

        // Rentang default jika tidak ada input: tanggal 26 bulan lalu s.d 25 bulan ini
        $startDate = $request->input(
            'start_date',
            $target->copy()->subMonth()->day(26)->format('Y-m-d')
        );

        $endDate = $request->input(
            'end_date',
            $target->copy()->day(25)->format('Y-m-d')
        );

        /*
        |--------------------------------------------------------------------------
        | Base Query Absensi
        |--------------------------------------------------------------------------
        */
        $query = Attendance::with([
            'employee',
            'leaveType'
        ])
            // Melakukan Join ke tabel karyawan untuk memastikan hanya mengambil karyawan aktif & urutan nama yang valid
            ->join(
                'employees',
                'employees.id',
                '=',
                'attendances.employee_id'
            )
            ->where('employees.is_active', true)
            ->whereBetween('attendances.date', [$startDate, $endDate])
            ->orderBy('employees.full_name', 'asc')
            ->select('attendances.*');

        // Filter opsional jika user menyaring per individu karyawan
        if ($request->filled('employee_id')) {
            $query->where('attendances.employee_id', $request->employee_id);
        }

        // Mengambil kumpulan data riil dari database
        $rows = $query->get();

        /*
        |--------------------------------------------------------------------------
        | Pemrosesan Data per Karyawan (Collection Grouping)
        |--------------------------------------------------------------------------
        */
        $summary = $rows
            ->groupBy('employee_id')
            ->map(function ($items) {

                // Ambil data profil karyawan dari baris pertama barisan data group
                $employee = $items->first()->employee;

                return [
                    'employee' => $employee,

                    // Menghitung hari hadir regular
                    'present' => $items
                        ->where('status', 'present')
                        ->count(),

                    // Menghitung hari Alpha (Mangkir)
                    'alpha' => $items
                        ->where('status', 'alpha')
                        ->count(),

                    // 1. SAKIT: Status leave, tag izin, dan ada unsur kata 'sakit' di nama tipenya
                    'sick' => $items
                        ->filter(function ($row) {
                            return $row->status == 'leave'
                                && optional($row->leaveType)->tag == 'izin'
                                && str_contains(strtolower(optional($row->leaveType)->name), 'sakit');
                        })
                        ->count(),

                    // 2. IZIN: Status leave, tag izin, tapi BUKAN izin sakit
                    'permission' => $items
                        ->filter(function ($row) {
                            return $row->status == 'leave'
                                && optional($row->leaveType)->tag == 'izin'
                                && !str_contains(strtolower(optional($row->leaveType)->name), 'sakit');
                        })
                        ->count(),

                    // 3. CUTI: Status leave dan memiliki tag murni 'cuti'
                    'annual_leave' => $items
                        ->filter(function ($row) {
                            return $row->status == 'leave'
                                && optional($row->leaveType)->tag == 'cuti';
                        })
                        ->count(),

                    // Menghitung total WFA jika ada tag 'wfa'
                    'wfa' => $items
                        ->filter(function ($row) {
                            return $row->status == 'leave'
                                && optional($row->leaveType)->tag == 'wfa';
                        })
                        ->count(),

                    // Menghitung total Libur Nasional
                    'holiday' => $items
                        ->where('status', 'holiday')
                        ->count(),

                    // Menghitung total libur regular (Off Sched)
                    'off' => $items
                        ->where('status', 'off')
                        ->count(),

                    // ATURAN LATE: Menit > 0 dan kolom toleransi (is_idt) TIDAK bernilai true/1
                    'late' => $items
                        ->filter(function ($row) {
                            return $row->late_minutes > 0 && $row->is_idt != true;
                        })
                        ->count(),

                    // ATURAN MENIT LATE: Hanya menjumlahkan menit yang tidak terkena toleransi is_idt
                    'late_minutes' => $items
                        ->filter(function ($row) {
                            return $row->is_idt != true;
                        })
                        ->sum('late_minutes'),

                    // ATURAN EARLY LEAVE: Menit > 0 dan kolom toleransi (is_ipc) TIDAK bernilai true/1
                    'early_leave' => $items
                        ->filter(function ($row) {
                            return $row->early_leave_minutes > 0 && $row->is_ipc != true;
                        })
                        ->count(),

                    // ATURAN MENIT EARLY LEAVE: Hanya menjumlahkan menit yang tidak terkena toleransi is_ipc
                    'early_leave_minutes' => $items
                        ->filter(function ($row) {
                            return $row->is_ipc != true;
                        })
                        ->sum('early_leave_minutes'),

                    // Menghitung kasus Lupa Absen Masuk
                    'forgot_in' => $items
                        ->where('forgot_check_in', true)
                        ->count(),

                    // Menghitung kasus Lupa Absen Keluar
                    'forgot_out' => $items
                        ->where('forgot_check_out', true)
                        ->count(),

                    // Menghitung penanda khusus Izin Pulang Cepat (IPC) murni
                    'ipc' => $items
                        ->where('is_ipc', true)
                        ->count(),

                    // Menghitung penanda khusus Izin Datang Terlambat (IDT) murni
                    'idt' => $items
                        ->where('is_idt', true)
                        ->count(),

                    // Total durasi menit kerja
                    'work_minutes' => $items
                        ->sum('work_minutes'),

                    // Total kekurangan Hari Kerja (diambil dari total Alpha)
                    'kurang_hk' => $items
                        ->where('status', 'alpha')
                        ->count(),

                    // Kalkulasi akumulasi total kekurangan jam kerja dalam satuan menit
                    'kurang_jam' => max(
                        0,
                        ($items->where('status', 'present')->count() * 480) - $items->sum('work_minutes')
                    ),
                ];
            })
            // Mengurutkan kembali hasil summary berdasarkan nama lengkap karyawan secara natural (A-Z)
            ->sortBy(function ($item) {
                return $item['employee']->full_name;
            }, SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Pembuatan Data Atas (Seksi Card Dasbor)
        |--------------------------------------------------------------------------
        */
        // 1. Hitung total menit keterlambatan & pulang cepat dari seluruh karyawan
        $totalLateMinutes = $summary->sum('late_minutes');
        $totalEarlyLeaveMinutes = $summary->sum('early_leave_minutes');

        $cards = [
            'employee' => $summary->count(),
            'present' => $summary->sum('present'),
            'alpha' => $summary->sum('alpha'),
            'cuti' => $summary->sum('annual_leave'),
            'izin' => $summary->sum('permission'),
            'sick' => $summary->sum('sick'),
            'holiday' => $summary->sum('holiday'),
            'off' => $summary->sum('off'),
            'forgot_in' => $summary->sum('forgot_in'),
            'forgot_out' => $summary->sum('forgot_out'),
            'idt' => $summary->sum('idt'),
            'ipc' => $summary->sum('ipc'),
            'kurang_hk' => $summary->sum('kurang_hk'),
            'kurang_jam' => $summary->sum('kurang_jam'),

            // Data Perbaikan Keterlambatan Global
            'late' => $summary->sum('late'),
            'total_late_minutes' => $totalLateMinutes,
            'late_hours' => floor($totalLateMinutes / 60),
            'late_minutes_remainder' => $totalLateMinutes % 60,

            // Data Perbaikan Pulang Cepat Global (Sudah diperbaiki dari sum('late') ke sum('early_leave'))
            'early_leave' => $summary->sum('early_leave'),
            'total_early_leave_minutes' => $totalEarlyLeaveMinutes,
            'early_leave_hours' => floor($totalEarlyLeaveMinutes / 60),
            'early_leave_minutes_remainder' => $totalEarlyLeaveMinutes % 60,
        ];

        /*
        |--------------------------------------------------------------------------
        | Kalkulasi Kalender Kerja Efektif
        |--------------------------------------------------------------------------
        */
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $calendarDays = $start->diffInDays($end) + 1;
        $sundayCount = 0;
        $current = $start->copy();

        // Loop penentuan jumlah hari Minggu di dalam range periode aktif
        while ($current->lte($end)) {
            if ($current->isSunday()) {
                $sundayCount++;
            }
            $current->addDay();
        }

        // Mengambil total libur nasional dari tabel master Holiday dalam range tanggal terpilih
        $holidayCount = Holiday::whereBetween('date_applied', [$startDate, $endDate])->count();

        // Rumus penentuan sisa hari kerja efektif perusahaan
        $workingDays = $calendarDays - $sundayCount - $holidayCount;

        // Mengirimkan seluruh variabel siap pakai ke dalam View Monthly-Attendance
        return view(
            'attendance-monthly.index',
            compact(
                'summary',
                'cards',
                'employees',
                'selectedYear',
                'selectedMonth',
                'startDate',
                'endDate',
                'workingDays',
                'calendarDays',
                'sundayCount',
                'holidayCount'
            )
        );
    }

    public function show(Request $request, Employee $employee)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if (!$startDate || !$endDate) {

            return redirect()
                ->route('attendance-monthly.index')
                ->with('error', 'Periode tidak ditemukan.');

        }

        $attendances = Attendance::with([
            'shift',
            'leaveType'
        ])
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [
                $startDate,
                $endDate
            ])
            ->orderBy('date', 'desc')
            ->get();

        $summary = [

            'present' => $attendances->where('status', 'present')->count(),

            'late' => $attendances->where('late_minutes', '>', 0)->count(),

            'alpha' => $attendances->where('status', 'alpha')->count(),

            'holiday' => $attendances->where('status', 'holiday')->count(),

            'off' => $attendances->where('status', 'off')->count(),

            'forgot_in' => $attendances->where('forgot_check_in', true)->count(),

            'forgot_out' => $attendances->where('forgot_check_out', true)->count(),

            'idt' => $attendances->where('is_idt', true)->count(),

            'ipc' => $attendances->where('is_ipc', true)->count(),

            'cuti' => $attendances
                ->filter(function ($row) {

                    return $row->status == 'leave'
                        && optional($row->leaveType)->tag == 'cuti';

                })
                ->count(),

            'izin' => $attendances
                ->filter(function ($row) {

                    return $row->status == 'leave'
                        && optional($row->leaveType)->tag == 'izin';

                })
                ->count(),

            'work_minutes' => $attendances->sum('work_minutes'),

            'late_minutes' => $attendances->sum('late_minutes'),

            'early_leave_minutes' => $attendances->sum('early_leave_minutes'),

        ];

        return view(
            'attendance-monthly.show',
            compact(
                'employee',
                'attendances',
                'summary',
                'startDate',
                'endDate'
            )
        );
    }
}