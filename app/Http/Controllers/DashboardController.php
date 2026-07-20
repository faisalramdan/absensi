<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeContract;
use App\Models\LeaveAllocation;
use App\Models\Attendance;
use App\Models\User;
use App\Models\LoginActivity;
use App\Models\LeaveRequest; // Pastikan model ini diimport
use App\Models\ShiftAssignment;
use App\Models\Holiday;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->can('dashboard.admin')) {
            return redirect()->route('dashboard.admin');
        }

        if ($user->can('dashboard.employee')) {
            return redirect()->route('dashboard.employee');
        }

        abort(403);
    }

    public function employeeDashboard(Request $request)
    {
        // Ambil data employee milik user yang sedang login
        $employee = auth()->user()->employee;

        if (!$employee) {
            abort(403, 'Data karyawan tidak ditemukan atau belum terhubung dengan akun login Anda.');
        }

        /*
        |--------------------------------------------------------------------------
        | Tentukan Periode Tanggal Berdasarkan Filter Request
        |--------------------------------------------------------------------------
        */
        $selectedYear = $request->input('year', date('Y'));
        $selectedMonth = $request->input('month', date('m'));

        // Membuat objek basis Carbon dari bulan & tahun terpilih
        $targetDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);

        // Hitung default tanggal cut-off (26 bulan lalu s/d 25 bulan ini)
        $defaultStart = $targetDate->copy()->subMonth()->day(26)->format('Y-m-d');
        $defaultEnd = $targetDate->copy()->day(25)->format('Y-m-d');

        // Pastikan formatnya selalu Y-m-d murni (Mencegah bug format string jam)
        $startDateInput = Carbon::parse($request->input('start_date', $defaultStart))->format('Y-m-d');
        $endDateInput = Carbon::parse($request->input('end_date', $defaultEnd))->format('Y-m-d');

        /*
        |--------------------------------------------------------------------------
        | Ambil Data Karyawan & Absensi TERLEBIH DAHULU (Sebelum Variabel Digeser Period)
        |--------------------------------------------------------------------------
        */
        $myAttendances = Attendance::with(['leaveType'])
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$startDateInput, $endDateInput])
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Buat Daftar Tanggal Periode untuk Kalender / Tabel Shift
        |--------------------------------------------------------------------------
        */
        $period = CarbonPeriod::create($startDateInput, $endDateInput);
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil Jadwal Shift & Hari Libur
        |--------------------------------------------------------------------------
        */
        $holidays = Holiday::whereBetween('date_applied', [$startDateInput, $endDateInput])
            ->pluck('name', 'date_applied')
            ->toArray();

        $assignments = ShiftAssignment::with('shift')
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$startDateInput, $endDateInput])
            ->get();

        $myAssignments = [];
        foreach ($assignments as $assignment) {
            $formattedDate = Carbon::parse($assignment->date)->format('Y-m-d');
            $myAssignments[$formattedDate] = [
                'shift_name' => $assignment->shift->name ?? '-',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Data Kontrak & Sisa Cuti
        |--------------------------------------------------------------------------
        */
        $activeContract = EmployeeContract::where('employee_id', $employee->id)
            ->where('is_active', true)
            ->latest()
            ->first();

        $leaveAllocations = collect();
        if ($activeContract) {
            $leaveAllocations = LeaveAllocation::with('leaveType')
                ->where('employee_contract_id', $activeContract->id)
                ->get();
        }

        $pendingLeaves = $employee->leaveRequests()->where('status', 'pending')->count();
        $approvedLeaves = $employee->leaveRequests()->where('status', 'approved')->count();
        $rejectedLeaves = $employee->leaveRequests()->where('status', 'rejected')->count();

        /*
        |--------------------------------------------------------------------------
        | KALKULASI METRIK (DIOPTIMALKAN UNTUK MENANGKAP STATUS LEAVE & HOLIDAY)
        |--------------------------------------------------------------------------
        */
        $totalLateMinutes = $myAttendances->filter(function ($item) {
            return (int) $item->late_minutes > 0 && !$item->is_idt;
        })->sum('late_minutes');

        $totalEarlyLeaveMinutes = $myAttendances->filter(function ($item) {
            return (int) $item->early_leave_minutes > 0 && !$item->is_ipc;
        })->sum('early_leave_minutes');

        // 1. Total Hari Kalender berdasarkan filter tanggal asli
        $calendarDays = \Carbon\Carbon::parse($startDateInput)->diffInDays(\Carbon\Carbon::parse($endDateInput)) + 1;

        // 2. Hitung jumlah hari libur akhir pekan (Off) murni dari kalender Carbon
        $period = \Carbon\CarbonPeriod::create($startDateInput, $endDateInput);
        $sundayCount = 0;
        foreach ($period as $date) {
            if ($date->isSunday()) {
                $sundayCount++;
            }
        }

        // 3. Menghitung Hari Libur Nasional (Holiday) yang terdata di absensi
        $holidayCount = $myAttendances->filter(fn($i) => strtolower($i->status) === 'holiday')->count();

        // 4. Hari Kerja Resmi (Total Kalender - Hari Off Akhir Pekan - Libur Nasional)
        $workingDays = $calendarDays - $sundayCount - $holidayCount;

        $cards = [
            'present' => $myAttendances->filter(fn($i) => strtolower($i->status) === 'present')->count(),
            'wfa' => $myAttendances->filter(fn($i) => strtolower($i->status) === 'wfa')->count(),
            'sakit' => $myAttendances->filter(fn($i) => in_array(strtolower($i->status), ['sick', 'sakit']))->count(),
            'alpha' => $myAttendances->filter(fn($i) => strtolower($i->status) === 'alpha')->count(),

            'cuti' => $myAttendances->filter(function ($item) {
                return strtolower($item->status) === 'leave'
                    && strtolower(optional($item->leaveType)->tag) === 'cuti';
            })->count(),

            'ijin' => $myAttendances->filter(function ($item) {
                if (strtolower($item->status) !== 'leave') {
                    return false;
                }

                $tag = strtolower(optional($item->leaveType)->tag);
                $code = strtoupper(optional($item->leaveType)->code);

                if ($tag === 'izin' && !in_array($code, ['I-IDT', 'I-IPC', 'I-SKT'])) {
                    return true;
                }

                if (empty($tag) || $tag !== 'cuti') {
                    return true;
                }

                return false;
            })->count(),

            'forgot_in' => $myAttendances->where('forgot_check_in', true)->count(),
            'forgot_out' => $myAttendances->where('forgot_check_out', true)->count(),
            'idt' => $myAttendances->where('is_idt', true)->count(),
            'ipc' => $myAttendances->where('is_ipc', true)->count(),

            'holiday' => $myAttendances->filter(fn($i) => strtolower($i->status) === 'holiday')->count(),
            'off' => $myAttendances->filter(fn($i) => strtolower($i->status) === 'off')->count(),

            'late' => $myAttendances->filter(function ($item) {
                return (int) $item->late_minutes > 0 && !$item->is_idt;
            })->count(),

            'early_leave' => $myAttendances->filter(function ($item) {
                return (int) $item->early_leave_minutes > 0 && !$item->is_ipc;
            })->count(),

            'short_work_count' => $myAttendances->filter(function ($item) {
                return (int) $item->short_work_minutes > 0
                    && !in_array(strtolower($item->status), ['wfa', 'holiday', 'off', 'leave'])
                    && !$item->is_idt
                    && !$item->is_ipc;
            })->count(),

            'total_work_minutes' => $myAttendances->filter(fn($i) => strtolower($i->status) === 'present')->sum('work_minutes'),
            'total_late_minutes' => $totalLateMinutes,
            'late_hours' => floor($totalLateMinutes / 60),
            'late_minutes_remainder' => $totalLateMinutes % 60,

            'total_early_leave_minutes' => $totalEarlyLeaveMinutes,
            'early_leave_hours' => floor($totalEarlyLeaveMinutes / 60),
            'early_leave_minutes_remainder' => $totalEarlyLeaveMinutes % 60,

            'total_short_work_minutes' => $myAttendances->filter(function ($item) {
                return (int) $item->short_work_minutes > 0
                    && !in_array(strtolower($item->status), ['wfa', 'holiday', 'off', 'leave'])
                    && !$item->is_idt
                    && !$item->is_ipc;
            })->sum('short_work_minutes'),
        ];

        $summary = $cards;
        $summary['forgot_check_in'] = $cards['forgot_in'];
        $summary['forgot_check_out'] = $cards['forgot_out'];

        /*
        |--------------------------------------------------------------------------
        | [TAMBAHAN BARU]: Query Absensi untuk Tabel Log (Dengan Pagination)
        |--------------------------------------------------------------------------
        */
        $attendances = Attendance::with(['shift', 'leaveType'])
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$startDateInput, $endDateInput])
            ->orderBy('date', 'desc') // Urutkan dari tanggal terbaru
            ->paginate(10);           // Tampilkan 10 baris per halaman

        return view('dashboard.employee', compact(
            'employee',
            'activeContract',
            'leaveAllocations',
            'pendingLeaves',
            'approvedLeaves',
            'rejectedLeaves',
            'dates',
            'myAssignments',
            'holidays',
            'selectedYear',
            'selectedMonth',
            'summary',
            'workingDays',
            'calendarDays',
            'sundayCount',
            'holidayCount',
            'attendances' // <--- Jangan lupa masukkan variabel baru ini
        ))->with([
                    'startDate' => $startDateInput,
                    'endDate' => $endDateInput
                ]);
    }

    public function adminDashboard()
    {
        $today = Carbon::today();

        // 1. Data Dasar
        $totalUsers = User::count();
        $totalEmployees = Employee::count();

        // 2. Data Absensi Hari Ini
        $hadirHariIni = Attendance::whereDate('date', $today)
            ->whereIn('status', ['present', 'wfa'])
            ->count();

        $izinCutiHariIni = Attendance::whereDate('date', $today)
            ->whereIn('status', ['sick', 'leave'])
            ->count();

        // 3. Statistik Grafik (6 Bulan Terakhir dengan Attendance Rate)
        $kpiData = [
            'physical' => [],
            'authorized' => [],
            'unauthorized' => [],
        ];
        $months = [];

        // Ambil tahun berjalan saat ini (misal: 2026)
        $currentYear = Carbon::now()->year;

        // Loop tetap dari bulan 1 (Januari) sampai 12 (Desember)
        for ($m = 1; $m <= 12; $m++) {
            // Tanggal akhir periode: Tanggal 25 di bulan berjalan ($m)
            $end = Carbon::create($currentYear, $m, 25)->endOfDay();

            // Tanggal awal periode: Tanggal 26 di bulan sebelumnya
            // Jika $m adalah 1 (Januari 2026), otomatis start menjadi 26 Desember 2025
            $start = (clone $end)->subMonth()->day(26)->startOfDay();

            $query = Attendance::whereBetween('date', [$start, $end]);

            // Total Hari Kerja efektif (Bukan hari Libur/Off)
            $totalJadwal = (clone $query)->whereNotIn('status', ['holiday', 'off'])->count();

            if ($totalJadwal > 0) {
                $hadir = (clone $query)->whereIn('status', ['present'])->count();
                $absensiSah = (clone $query)->whereIn('status', ['sick', 'leave'])->count();
                $alpha = (clone $query)->where('status', 'alpha')->count();

                $kpiData['physical'][] = round(($hadir / $totalJadwal) * 100, 2);
                $kpiData['authorized'][] = round(($absensiSah / $totalJadwal) * 100, 2);
                $kpiData['unauthorized'][] = round(($alpha / $totalJadwal) * 100, 2);
            } else {
                // Jika bulan tersebut belum memiliki data (misal bulan depan/Agustus - Desember)
                $kpiData['physical'][] = 0;
                $kpiData['authorized'][] = 0;
                $kpiData['unauthorized'][] = 0;
            }

            // Format label tetap manis menuruti urutan Januari - Desember
            $months[] = $end->format('M Y');
        }

        // 4. Data Karyawan Terbaru
        $latestEmployees = Employee::with('position')
            ->orderByDesc('join_date')
            ->take(5)
            ->get();

        // 5. Data Kontrak Terbaru
        $latestContracts = \App\Models\EmployeeContract::with(['employee', 'employeeStatus'])
            ->latest()
            ->take(5)
            ->get();

        // 6. Data Log Aktivitas
        $latestLogins = LoginActivity::latest('logged_at')
            ->take(5)
            ->get();

        // ==========================================
        // 7. Data Cuti Bulan Ini (Approved)
        // ==========================================
        // Catatan: Pastikan nama modelnya adalah 'LeaveRequest' (sesuaikan jika berbeda).
        // Saya menambahkan \App\Models\ di depannya agar tidak terjadi error "Class not found".
        // 7. Data Cuti Bulan Ini (Approved)
        $approvedLeaves = \App\Models\LeaveRequest::with(['employee', 'leaveType']) // <-- Pastikan 'leaveType' dipanggil di sini
            ->where('status', 'approved')
            ->whereMonth('start_date', $today->month)
            ->whereYear('start_date', $today->year)
            ->orderBy('start_date', 'asc')
            ->get();

        // 8. Data Ulang Tahun Bulan Ini
        $upcomingBirthdays = \App\Models\Employee::whereMonth('birth_date', Carbon::now()->month)
            ->orderByRaw('DAY(birth_date) asc') // Mengurutkan berdasarkan tanggal terkecil ke terbesar
            ->get();


        return view(
            'dashboard.admin',
            compact(
                'totalUsers',
                'totalEmployees',
                'latestEmployees',
                'latestContracts',
                'latestLogins',
                'hadirHariIni',
                'izinCutiHariIni',
                'kpiData',
                'months',
                'approvedLeaves', // <-- Tambahan variabel untuk dikirim ke file Blade
                'upcomingBirthdays' // <-- Tambahan variabel untuk dikirim ke file Blade
            )
        );
    }

    public function getScheduleEvents(\Illuminate\Http\Request $request)
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            return response()->json([]);
        }

        // FullCalendar otomatis mengirim parameter range tanggal yang sedang dibuka (start & end)
        $start = $request->input('start');
        $end = $request->input('end');

        // 1. Ambil data libur nasional pada range tanggal tersebut
        $holidays = \App\Models\Holiday::whereBetween('date_applied', [$start, $end])
            ->pluck('name', 'date_applied')
            ->toArray();

        // 2. Ambil data jadwal shift karyawan pada range tanggal tersebut
        $assignments = \App\Models\ShiftAssignment::with('shift')
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$start, $end])
            ->get();

        $calendarEvents = [];

        // 3. Masukkan Jadwal Shift ke array event
        foreach ($assignments as $assignment) {
            $formattedDate = \Carbon\Carbon::parse($assignment->date)->format('Y-m-d');
            $shiftName = $assignment->shift->name ?? '-';
            $shiftLower = strtolower($shiftName);

            $className = 'bg-secondary';
            if (str_contains($shiftLower, 'pagi')) {
                $className = 'bg-success';
            } elseif (str_contains($shiftLower, 'siang')) {
                $className = 'bg-primary';
            } elseif (str_contains($shiftLower, 'malam')) {
                $className = 'bg-info';
            } elseif (str_contains($shiftLower, 'off') || str_contains($shiftLower, 'libur')) {
                $className = 'bg-danger';
            }

            $calendarEvents[] = [
                'title' => $shiftName,
                'start' => $formattedDate,
                'className' => [$className, 'p-1', 'fw-semibold', 'border-0']
            ];
        }

        // 4. Masukkan Hari Libur Nasional ke array event
        foreach ($holidays as $holidayDate => $holidayName) {
            $calendarEvents[] = [
                'title' => '🎉 ' . $holidayName,
                'start' => $holidayDate,
                'className' => ['bg-danger', 'p-1', 'fw-semibold', 'border-0']
            ];
        }

        return response()->json($calendarEvents);
    }
}