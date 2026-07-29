<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\Holiday;
use App\Models\Company;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get();

        // 1. Ambil company_id milik user yang sedang login
        $userCompanyId = auth()->user()->employee?->company_id
            ?? Employee::where('user_id', auth()->id())->value('company_id');

        // 2. Tentukan company_id yang dipakai (dari request/filter atau default user login)
        $selectedCompanyId = $request->has('company_id')
            ? $request->company_id
            : $userCompanyId;

        // Mengambil daftar karyawan yang aktif untuk filter dropdown pada view
        $employees = Employee::where('is_active', true)
            ->orderBy('full_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Periode Default (26 - 25)
        |--------------------------------------------------------------------------
        | Mengatur cut-off default absensi dari tanggal 26 bulan lalu hingga 25 bulan ini
        */

        $selectedYear = $request->input('year', date('Y'));
        $selectedMonth = $request->input('month', date('m'));

        // Membuat objek Carbon berdasarkan tahun dan bulan yang dipilih (set tanggal ke 1)
        $targetDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);

        // Default mulai: Tanggal 26 dari 1 bulan sebelum bulan yang dipilih
        $defaultStart = $targetDate
            ->copy()
            ->subMonth()
            ->day(26)
            ->format('Y-m-d');

        // Default akhir: Tanggal 25 pada bulan yang dipilih
        $defaultEnd = $targetDate
            ->copy()
            ->day(25)
            ->format('Y-m-d');

        /*
        |--------------------------------------------------------------------------
        | Tanggal Aktif
        |--------------------------------------------------------------------------
        | Menggunakan tanggal inputan user (jika ada) atau menggunakan periode default
        */

        $startDate = $request->input('start_date', $defaultStart);
        $endDate = $request->input('end_date', $defaultEnd);

        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        | Membangun kueri dasar absensi beserta relasi tabel terkait
        */

        $query = Attendance::with([
            'employee',
            'shift',
            'leaveType'
        ]);

        // Filter berdasarkan rentang tanggal yang aktif
        $query->whereBetween('date', [
            $startDate,
            $endDate
        ]);

        // 3. Filter berdasarkan Company (Default User Login / Dropdown pilihan)
        if (!empty($selectedCompanyId)) {
            $query->whereHas('employee', function ($q) use ($selectedCompanyId) {
                $q->where('company_id', $selectedCompanyId);
            });
        }

        // Kondisi opsional: Filter berdasarkan karyawan tertentu jika dipilih
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Kondisi opsional: Filter berdasarkan status absensi tertentu jika dipilih
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        /*
        |--------------------------------------------------------------------------
        | Summary (Kalkulasi Metrik)
        |--------------------------------------------------------------------------
        | Menghitung akumulasi data absensi menggunakan teknik cloning query
        */

        $summary = [
            // Menghitung total kehadiran regular
            'present' => (clone $query)
                ->where('status', 'present')
                ->count(),

            // Menghitung total kehadiran WFA
            'wfa' => (clone $query)
                ->where('status', 'wfa')
                ->count(),

            // 1. SAKIT
            'sakit' => (clone $query)
                ->where('status', 'sick')
                ->count(),

            // 2. IJIN
            'ijin' => (clone $query)
                ->where('status', 'leave')
                ->whereHas('leaveType', function ($q) {
                    $q->where('tag', 'izin')
                        ->whereNotIn('code', ['I-IDT', 'I-IPC', 'I-SKT']);
                })
                ->count(),

            // 3. CUTI
            'cuti' => (clone $query)
                ->where('status', 'leave')
                ->whereHas('leaveType', function ($q) {
                    $q->where('tag', 'cuti');
                })
                ->count(),

            // Menghitung total ketidakhadiran tanpa keterangan (Mangkir)
            'alpha' => (clone $query)
                ->where('status', 'alpha')
                ->count(),

            // Menghitung total keterlambatan
            'late' => (clone $query)
                ->where('late_minutes', '>', 0)
                ->where(function ($q) {
                    $q->where('is_idt', '!=', true)
                        ->orWhereNull('is_idt');
                })
                ->count(),

            // Menghitung total pulang cepat
            'early_leave' => (clone $query)
                ->where('early_leave_minutes', '>', 0)
                ->where(function ($q) {
                    $q->where('is_ipc', '!=', true)
                        ->orWhereNull('is_ipc');
                })
                ->count(),

            // Menghitung total lupa melakukan absen masuk
            'forgot_check_in' => (clone $query)
                ->where('forgot_check_in', true)
                ->count(),

            // Menghitung total lupa melakukan absen keluar
            'forgot_check_out' => (clone $query)
                ->where('forgot_check_out', true)
                ->count(),

            // Menghitung total hari Libur Nasional
            'holiday' => (clone $query)
                ->where('status', 'holiday')
                ->count(),

            // Menghitung total hari libur regular
            'off' => (clone $query)
                ->where('status', 'off')
                ->count(),

            // Menghitung akumulasi menit waktu kerja efektif
            'total_work_minutes' => (clone $query)
                ->where('status', 'present')
                ->sum('work_minutes'),

            // Menghitung penanda khusus Izin Pulang Cepat (IPC) murni
            'ipc' => (clone $query)
                ->where('is_ipc', true)
                ->count(),

            // Menghitung penanda khusus Izin Datang Terlambat (IDT) murni
            'idt' => (clone $query)
                ->where('is_idt', true)
                ->count(),

            // Menghitung total akumulasi menit keterlambatan
            'total_late_minutes' => (clone $query)
                ->where('late_minutes', '>', 0)
                ->where(function ($q) {
                    $q->where('is_idt', '!=', true)
                        ->orWhereNull('is_idt');
                })
                ->sum('late_minutes'),

            // Menghitung berapa kali karyawan kurang jam kerja
            'short_work_count' => (clone $query)
                ->where('short_work_minutes', '>', 0)
                ->whereNotIn('status', ['wfa', 'holiday', 'off'])
                ->where(function ($q) {
                    $q->where('is_idt', '!=', true)->orWhereNull('is_idt');
                })
                ->where(function ($q) {
                    $q->where('is_ipc', '!=', true)->orWhereNull('is_ipc');
                })
                ->count(),

            // Menghitung total akumulasi menit kurang jam kerja
            'total_short_work_minutes' => (clone $query)
                ->where('short_work_minutes', '>', 0)
                ->whereNotIn('status', ['wfa', 'holiday', 'off'])
                ->where(function ($q) {
                    $q->where('is_idt', '!=', true)->orWhereNull('is_idt');
                })
                ->where(function ($q) {
                    $q->where('is_ipc', '!=', true)->orWhereNull('is_ipc');
                })
                ->sum('short_work_minutes'),
        ];

        /*
        |--------------------------------------------------------------------------
        | Konversi Waktu Durasi (Late & Early Leave)
        |--------------------------------------------------------------------------
        */

        $summary['late_hours'] = floor($summary['total_late_minutes'] / 60);
        $summary['late_minutes_remainder'] = $summary['total_late_minutes'] % 60;

        $summary['total_early_leave_minutes'] = (clone $query)
            ->where('early_leave_minutes', '>', 0)
            ->where(function ($q) {
                $q->where('is_ipc', '!=', true)
                    ->orWhereNull('is_ipc');
            })
            ->sum('early_leave_minutes');

        $summary['early_leave_hours'] = floor($summary['total_early_leave_minutes'] / 60);
        $summary['early_leave_minutes_remainder'] = $summary['total_early_leave_minutes'] % 60;

        /*
        |--------------------------------------------------------------------------
        | Hari Kerja (Kalender Otomatis)
        |--------------------------------------------------------------------------
        */

        $workingDays = 0;
        $sundayCount = 0;
        $holidayCount = 0;

        $calendarDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $current = Carbon::parse($startDate);

        while ($current->lte(Carbon::parse($endDate))) {
            if ($current->dayOfWeek == Carbon::SUNDAY) {
                $sundayCount++;
            } else {
                $holiday = Holiday::whereDate('date_applied', $current)->exists();

                if ($holiday) {
                    $holidayCount++;
                } else {
                    $workingDays++;
                }
            }
            $current->addDay();
        }

        /*
        |--------------------------------------------------------------------------
        | Attendance Table Data
        |--------------------------------------------------------------------------
        */

        $attendances = $query
            ->orderBy('date', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view(
            'attendances.index',
            compact(
                'attendances',
                'companies',
                'employees',
                'selectedYear',
                'selectedMonth',
                'startDate',
                'endDate',
                'summary',
                'workingDays',
                'calendarDays',
                'sundayCount',
                'holidayCount',
                'selectedCompanyId' // 4. Dikirim ke Blade agar opsi dropdown perusahaan tetap terpilih
            )
        );
    }

    public function show(
        Attendance $attendance
    ) {
        $attendance->load([
            'employee',
            'shift',
            'leaveType',
            'leaveRequest'
        ]);

        return view(
            'attendances.show',
            compact('attendance')
        );
    }
}