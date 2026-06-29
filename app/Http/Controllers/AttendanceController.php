<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\Holiday;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
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

            // 1. SAKIT: Status 'leave', tag 'izin' di tabel leave_types, dan nama mengandung kata 'sakit'
            'sakit' => (clone $query)
                ->where('status', 'leave')
                ->whereHas('leaveType', function ($q) {
                    $q->where('tag', 'izin')
                        ->where('name', 'like', '%sakit%');
                })
                ->count(),

            // 2. IJIN: Status 'leave', tag 'izin' di tabel leave_types, tetapi BUKAN izin sakit
            'ijin' => (clone $query)
                ->where('status', 'leave')
                ->whereHas('leaveType', function ($q) {
                    $q->where('tag', 'izin')
                        ->where('name', 'not like', '%sakit%');
                })
                ->count(),

            // 3. CUTI: Status 'leave' dan memiliki tag 'cuti' di tabel leave_types
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

            // Menghitung total keterlambatan (is_idt = true DIABAIKAN karena dianggap toleransi)
            'late' => (clone $query)
                ->where('late_minutes', '>', 0)
                ->where(function ($q) {
                    $q->where('is_idt', '!=', true)
                        ->orWhereNull('is_idt');
                })
                ->count(),

            // Menghitung total pulang cepat (is_ipc = true DIABAIKAN karena dianggap toleransi)
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

            // Menghitung total hari Libur Nasional yang masuk dalam jadwal kerja
            'holiday' => (clone $query)
                ->where('status', 'holiday')
                ->count(),

            // Menghitung total hari libur regular (misal: jadwal off shift)
            'off' => (clone $query)
                ->where('status', 'off')
                ->count(),

            // Menghitung akumulasi menit waktu kerja efektif
            'total_work_minutes' => (clone $query)
                ->sum('work_minutes'),

            // Menghitung penanda khusus Izin Pulang Cepat (IPC) murni
            'ipc' => (clone $query)
                ->where('is_ipc', true)
                ->count(),

            // Menghitung penanda khusus Izin Datang Terlambat (IDT) murni
            'idt' => (clone $query)
                ->where('is_idt', true)
                ->count(),

            // Menghitung total akumulasi menit keterlambatan (Menit is_idt = true TIDAK DIHITUNG)
            'total_late_minutes' => (clone $query)
                ->where('late_minutes', '>', 0)
                ->where(function ($q) {
                    $q->where('is_idt', '!=', true)
                        ->orWhereNull('is_idt');
                })
                ->sum('late_minutes'),
        ];

        /*
        |--------------------------------------------------------------------------
        | Konversi Waktu Durasi (Late & Early Leave)
        |--------------------------------------------------------------------------
        */

        // Mengonversi total menit keterlambatan ke satuan Jam dan sisa Menit
        $summary['late_hours'] = floor($summary['total_late_minutes'] / 60);
        $summary['late_minutes_remainder'] = $summary['total_late_minutes'] % 60;

        // Menghitung total menit pulang cepat (Menit is_ipc = true TIDAK DIHITUNG)
        $summary['total_early_leave_minutes'] = (clone $query)
            ->where('early_leave_minutes', '>', 0)
            ->where(function ($q) {
                $q->where('is_ipc', '!=', true)
                    ->orWhereNull('is_ipc');
            })
            ->sum('early_leave_minutes');

        // Mengonversi total menit pulang cepat ke satuan Jam dan sisa Menit
        $summary['early_leave_hours'] = floor($summary['total_early_leave_minutes'] / 60);
        $summary['early_leave_minutes_remainder'] = $summary['total_early_leave_minutes'] % 60;

        /*
        |--------------------------------------------------------------------------
        | Hari Kerja (Kalender Otomatis)
        |--------------------------------------------------------------------------
        | Menghitung total hari riil, hari minggu, hari libur nasional, dan hari kerja efektif
        */

        $workingDays = 0;
        $sundayCount = 0;
        $holidayCount = 0;

        // Menghitung selisih total hari kalender di dalam range tanggal aktif
        $calendarDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $current = Carbon::parse($startDate);

        // Loop harian untuk memeriksa status penanggalan
        while ($current->lte(Carbon::parse($endDate))) {
            if ($current->dayOfWeek == Carbon::SUNDAY) {
                // Jika hari minggu, masukkan ke counter Off
                $sundayCount++;
            } else {
                // Jika bukan hari minggu, cek apakah terdaftar di tabel master Holiday
                $holiday = Holiday::whereDate('date_applied', $current)->exists();

                if ($holiday) {
                    $holidayCount++;
                } else {
                    // Jika bukan minggu dan bukan libur nasional, maka dihitung sebagai Hari Kerja Efektif
                    $workingDays++;
                }
            }
            $current->addDay(); // Lanjut ke tanggal berikutnya
        }

        /*
        |--------------------------------------------------------------------------
        | Attendance Table Data
        |--------------------------------------------------------------------------
        | Mengambil data baris absensi utama untuk ditampilkan dalam bentuk tabel/list
        */

        $attendances = $query
            ->orderBy('date', 'desc')
            ->paginate(20)
            ->withQueryString(); // Memastikan parameter filter request (search/page) tidak hilang saat pindah halaman paginasi

        // Melempar seluruh variabel terproses ke view blade
        return view(
            'attendances.index',
            compact(
                'attendances',
                'employees',
                'selectedYear',
                'selectedMonth',
                'startDate',
                'endDate',
                'summary',
                'workingDays',
                'calendarDays',
                'sundayCount',
                'holidayCount'
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