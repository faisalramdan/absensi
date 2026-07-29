<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Company;
use App\Models\Attendance;
use App\Models\Holiday;

class AttendanceMonthlyController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get();

        // 1. Ambil company_id milik user yang sedang login
        $userCompanyId = auth()->user()->employee?->company_id
            ?? \App\Models\Employee::where('user_id', auth()->id())->value('company_id');

        // 2. Tentukan company_id yang dipakai (dari request/filter atau default user login)
        $selectedCompanyId = $request->has('company_id')
            ? $request->company_id
            : $userCompanyId;

        // Mengambil semua karyawan aktif untuk dropdown filter di view
        $employees = Employee::where('is_active', true)
            ->orderBy('full_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Periode Default (Cut-off 26 - 25)
        |--------------------------------------------------------------------------
        */
        $selectedYear = $request->get('year', date('Y'));
        $selectedMonth = $request->get('month', date('m'));

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

        $startDate = $request->get('start_date', $defaultStart);
        $endDate = $request->get('end_date', $defaultEnd);

        /*
        |--------------------------------------------------------------------------
        | Base Query Absensi
        |--------------------------------------------------------------------------
        */
        $query = Attendance::with([
            'employee',
            'leaveType'
        ])
            ->join('employees', 'employees.id', '=', 'attendances.employee_id')
            ->where('employees.is_active', true)
            ->whereBetween('attendances.date', [$startDate, $endDate])
            ->orderBy('employees.full_name', 'asc')
            ->select('attendances.*');

        // 3. FILTER BARU: Berdasarkan Perusahaan (Company Default Login / Dropdown)
        if (!empty($selectedCompanyId)) {
            $query->whereHas('employee', function ($q) use ($selectedCompanyId) {
                $q->where('company_id', $selectedCompanyId);
            });
        }

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
                // Ambil data profil karyawan
                $employee = $items->first()->employee;

                return [
                    'employee' => $employee,

                    // Menghitung hari hadir regular
                    'present' => $items->where('status', 'present')->count(),

                    // Menghitung hari WFA sesuai standar tabel harian (status = 'wfa')
                    'wfa' => $items->where('status', 'wfa')->count(),

                    // Menghitung hari Alpha (Mangkir)
                    'alpha' => $items->where('status', 'alpha')->count(),

                    // 1. SAKIT: Status leave, tag izin, dan ada unsur kata 'sakit' di nama tipenya
                    'sick' => $items->filter(function ($row) {
                        return $row->status == 'sick'
                            && optional($row->leaveType)->tag == 'izin'
                            && str_contains(strtolower(optional($row->leaveType)->name), 'sakit');
                    })->count(),

                    // 2. IZIN: Status leave, tag izin, tapi BUKAN izin sakit
                    'permission' => $items->filter(function ($row) {
                        return $row->status == 'leave'
                            && optional($row->leaveType)->tag == 'izin'
                            && !str_contains(strtolower(optional($row->leaveType)->name), 'sakit');
                    })->count(),

                    // 3. CUTI: Status leave dan memiliki tag murni 'cuti'
                    'annual_leave' => $items->filter(function ($row) {
                        return $row->status == 'leave'
                            && optional($row->leaveType)->tag == 'cuti';
                    })->count(),

                    // Menghitung total Libur Nasional & Off regular
                    'holiday' => $items->where('status', 'holiday')->count(),
                    'off' => $items->where('status', 'off')->count(),

                    // ATURAN LATE: Menit > 0 dan kolom toleransi (is_idt) TIDAK bernilai true/1
                    'late' => $items->filter(function ($row) {
                        return $row->late_minutes > 0 && $row->is_idt != true;
                    })->count(),

                    // ATURAN MENIT LATE: Hanya menjumlahkan menit yang tidak terkena toleransi is_idt
                    'late_minutes' => $items->filter(function ($row) {
                        return $row->is_idt != true;
                    })->sum('late_minutes'),

                    // ATURAN EARLY LEAVE: Menit > 0 dan kolom toleransi (is_ipc) TIDAK bernilai true/1
                    'early_leave' => $items->filter(function ($row) {
                        return $row->early_leave_minutes > 0 && $row->is_ipc != true;
                    })->count(),

                    // ATURAN MENIT EARLY LEAVE: Hanya menjumlahkan menit yang tidak terkena toleransi is_ipc
                    'early_leave_minutes' => $items->filter(function ($row) {
                        return $row->is_ipc != true;
                    })->sum('early_leave_minutes'),

                    // Menghitung kasus Lupa Absen Masuk & Keluar
                    'forgot_in' => $items->where('forgot_check_in', true)->count(),
                    'forgot_out' => $items->where('forgot_check_out', true)->count(),

                    // Menghitung penanda khusus Izin Pulang Cepat (IPC) & Terlambat (IDT) murni
                    'ipc' => $items->where('is_ipc', true)->count(),
                    'idt' => $items->where('is_idt', true)->count(),

                    // Total durasi menit kerja
                    'work_minutes' => $items->where('status', 'present')->sum('work_minutes'),

                    // Total kekurangan Hari Kerja (diambil dari total Alpha)
                    'kurang_hk' => $items->where('status', 'alpha')->count(),

                    // 🔥 FIX AKURAT: Gunakan perbandingan nilai integer murni dari model attribute 
                    'short_work_count' => $items->filter(function ($item) {
                        $shortMinutes = (int) ($item->short_work_minutes ?? 0);
                        $isIdt = filter_var($item->is_idt, FILTER_VALIDATE_BOOLEAN);
                        $isIpc = filter_var($item->is_ipc, FILTER_VALIDATE_BOOLEAN);

                        return $shortMinutes > 0
                            && !in_array($item->status, ['wfa', 'holiday', 'off'])
                            && !$isIdt
                            && !$isIpc;
                    })->count(),

                    // 🔥 FIX AKURAT: Jumlahkan total menit dengan memetakan datanya ke bentuk int terlebih dahulu
                    'kurang_jam' => $items->filter(function ($item) {
                        $shortMinutes = (int) ($item->short_work_minutes ?? 0);
                        $isIdt = filter_var($item->is_idt, FILTER_VALIDATE_BOOLEAN);
                        $isIpc = filter_var($item->is_ipc, FILTER_VALIDATE_BOOLEAN);

                        return $shortMinutes > 0
                            && !in_array($item->status, ['wfa', 'holiday', 'off'])
                            && !$isIdt
                            && !$isIpc;
                    })->sum(function ($item) {
                        return (int) $item->short_work_minutes;
                    }),
                ];
            })
            ->sortBy(function ($item) {
                return $item['employee']->full_name;
            }, SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Pembuatan Data Atas (Seksi Card Dasbor)
        |--------------------------------------------------------------------------
        */
        $totalLateMinutes = $summary->sum('late_minutes');
        $totalEarlyLeaveMinutes = $summary->sum('early_leave_minutes');

        $cards = [
            'employee' => $summary->count(),
            'present' => $summary->sum('present'),
            'wfa' => $summary->sum('wfa'),
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

            // 🔥 FIX: Paksa penjumlahan menggunakan callback array untuk Card Dasbor
            'kurang_jam' => $summary->sum(function ($row) {
                return (int) ($row['kurang_jam'] ?? 0);
            }),

            'short_work_count' => $summary->sum(function ($row) {
                return (int) ($row['short_work_count'] ?? 0);
            }),

            // Data Keterlambatan Global
            'late' => $summary->sum('late'),
            'total_late_minutes' => $totalLateMinutes,
            'late_hours' => floor($totalLateMinutes / 60),
            'late_minutes_remainder' => $totalLateMinutes % 60,

            // Data Pulang Cepat Global
            'early_leave' => $summary->sum('early_leave'),
            'total_early_leave_minutes' => $totalEarlyLeaveMinutes,
            'early_leave_hours' => floor($totalEarlyLeaveMinutes / 60),
            'early_leave_minutes_remainder' => $totalEarlyLeaveMinutes % 60,
        ];

        /*
        |--------------------------------------------------------------------------
        | Hari Kerja (Kalender Otomatis - Sesuai Dengan Logika Daily)
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

        return view('attendance-monthly.index', compact(
            'summary',
            'cards',
            'companies',
            'employees',
            'selectedYear',
            'selectedMonth',
            'startDate',
            'endDate',
            'workingDays',
            'calendarDays',
            'sundayCount',
            'holidayCount',
            'selectedCompanyId' // 4. PASTIKAN DIKIRIM KE VIEW
        ));
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

        $attendances = Attendance::with(['shift', 'leaveType'])
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        $summary = [
            'present' => $attendances->where('status', 'present')->count(),
            'wfa' => $attendances->where('status', 'wfa')->count(),
            'alpha' => $attendances->where('status', 'alpha')->count(),
            'holiday' => $attendances->where('status', 'holiday')->count(),
            'off' => $attendances->where('status', 'off')->count(),
            'forgot_in' => $attendances->where('forgot_check_in', true)->count(),
            'forgot_out' => $attendances->where('forgot_check_out', true)->count(),
            'idt' => $attendances->where('is_idt', true)->count(),
            'ipc' => $attendances->where('is_ipc', true)->count(),
            'cuti' => $attendances->filter(function ($row) {
                return $row->status == 'leave' && optional($row->leaveType)->tag == 'cuti';
            })->count(),
            'izin' => $attendances->filter(function ($row) {
                return $row->status == 'leave' && optional($row->leaveType)->tag == 'izin';
            })->count(),
            'work_minutes' => $attendances->where('status', 'present')->sum('work_minutes'),
            'late_minutes' => $attendances->filter(function ($row) {
                return $row->is_idt != true;
            })->sum('late_minutes'),

            // 🔥 TAMBAHKAN KUNCI 'late' DI SINI (Menghitung frekuensi keterlambatan murni)
            'late' => $attendances->filter(function ($row) {
                return $row->late_minutes > 0 && $row->is_idt != true;
            })->count(),
            'early_leave_minutes' => $attendances->filter(function ($row) {
                return $row->is_ipc != true;
            })->sum('early_leave_minutes'),
            'short_work_count' => $attendances->filter(function ($row) {
                return $row->short_work_minutes > 0
                    && !in_array($row->status, ['wfa', 'holiday', 'off'])
                    && $row->is_idt != true
                    && $row->is_ipc != true;
            })->count(),
            'total_short_work_minutes' => $attendances->filter(function ($row) {
                return $row->short_work_minutes > 0
                    && !in_array($row->status, ['wfa', 'holiday', 'off'])
                    && $row->is_idt != true
                    && $row->is_ipc != true;
            })->sum('short_work_minutes'),
        ];

        return view('attendance-monthly.show', compact('employee', 'attendances', 'summary', 'startDate', 'endDate'));
    }
}