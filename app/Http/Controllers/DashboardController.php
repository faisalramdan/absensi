<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Models\LoginActivity;
use App\Models\ShiftAssignment; // 🌟 Pastikan Model ini di-import
use App\Models\Holiday;         // 🌟 Pastikan Model ini di-import
use Carbon\Carbon;              // 🌟 Pastikan Carbon di-import
use Carbon\CarbonPeriod;        // 🌟 Pastikan CarbonPeriod di-import

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->can('dashboard.admin')) {

            return redirect()->route(
                'dashboard.admin'
            );

        }

        if ($user->can('dashboard.employee')) {

            return redirect()->route(
                'dashboard.employee'
            );

        }

        abort(403);
    }

    public function employeeDashboard()
    {
        // Ambil data employee milik user yang sedang login
        $employee = auth()->user()->employee;

        if (!$employee) {
            abort(403, 'Data karyawan tidak ditemukan atau belum terhubung dengan akun login Anda.');
        }

        // --- 🌟 AWAL LOGIKA TAMBAHAN UNTUK JADWAL SHIFT KARYAWAN ---

        // 1. Tentukan periode bulan berjalan saat ini (Cut-off: 26 bulan lalu s/d 25 bulan ini)
        $currentMonth = date('m');
        $currentYear = date('Y');
        $startDate = Carbon::create($currentYear, $currentMonth, 26)->subMonth();
        $endDate = Carbon::create($currentYear, $currentMonth, 25);

        // Generate daftar tanggal periode untuk kolom header tabel
        $period = CarbonPeriod::create($startDate, $endDate);
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        // 2. Ambil data hari libur nasional pada rentang periode ini
        $holidays = Holiday::whereBetween('date_applied', [
            $startDate->startOfDay()->toDateTimeString(),
            $endDate->endOfDay()->toDateTimeString()
        ])->pluck('name', 'date_applied')->toArray();

        // 3. Ambil data jadwal KHUSUS karyawan yang sedang login ini
        $assignments = ShiftAssignment::with('shift')
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [
                $startDate->startOfDay()->toDateTimeString(),
                $endDate->endOfDay()->toDateTimeString()
            ])
            ->get();

        // Petakan ke array agar mudah dipanggil di Blade berdasarkan string tanggal: $myAssignments['Y-m-d']
        $myAssignments = [];
        foreach ($assignments as $assignment) {
            $formattedDate = Carbon::parse($assignment->date)->format('Y-m-d');
            $myAssignments[$formattedDate] = [
                'shift_name' => $assignment->shift->name ?? '-',
            ];
        }

        // --- 🌟 AKHIR LOGIKA TAMBAHAN UNTUK JADWAL SHIFT ---


        // 1. Ambil kontrak yang sedang aktif langsung via Model Contract
        $activeContract = \App\Models\EmployeeContract::where('employee_id', $employee->id)
            ->where('is_active', true)
            ->latest()
            ->first();

        // 2. Ambil data alokasi kuota berdasarkan kontrak aktif tersebut
        $leaveAllocations = collect();

        if ($activeContract) {
            $leaveAllocations = \App\Models\LeaveAllocation::with('leaveType')
                ->where('employee_contract_id', $activeContract->id)
                ->get();
        }

        // 3. Count data pengajuan cuti
        $pendingLeaves = $employee->leaveRequests()->where('status', 'pending')->count();
        $approvedLeaves = $employee->leaveRequests()->where('status', 'approved')->count();
        $rejectedLeaves = $employee->leaveRequests()->where('status', 'rejected')->count();

        // Kirim variabel tambahan ke view dashboard.employee
        return view('dashboard.employee', compact(
            'employee',
            'activeContract',
            'leaveAllocations',
            'pendingLeaves',
            'approvedLeaves',
            'rejectedLeaves',
            'dates',           // 🌟 Tambahan baru
            'myAssignments',   // 🌟 Tambahan baru
            'holidays',        // 🌟 Tambahan baru
            'startDate',       // 🌟 Tambahan baru
            'endDate'          // 🌟 Tambahan baru
        ));
    }

    public function adminDashboard()
    {
        $totalUsers = User::count();
        $totalEmployees = Employee::count();

        // 1. Ambil Karyawan Terbaru (Kembali ke kode asli Anda)
        $latestEmployees = Employee::with('position')
            ->orderByDesc('join_date')
            ->take(5)
            ->get();

        // 2. TAMBAHKAN: Ambil 5 Kontrak Karyawan Terbaru di Sistem
        $latestContracts = \App\Models\EmployeeContract::with(['employee', 'employeeStatus'])
            ->latest() // Mengurutkan berdasarkan yang terbaru diinput/diperbarui
            ->take(5)
            ->get();

        // Mengambil 5 data aktivitas terbaru (login, logout, failed_login)
        $latestLogins = LoginActivity::latest('logged_at')
            ->take(5)
            ->get();

        return view(
            'dashboard.admin',
            compact(
                'totalUsers',
                'totalEmployees',
                'latestEmployees',
                'latestContracts', // <--- Kirim variabel baru ini ke view
                'latestLogins'
            )
        );
    }

    // Paste ini di dalam class DashboardController (di bagian paling bawah sebelum penutup '}')

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
                'className' => [$className, 'p-1', 'fw-semibold', 'border-0'] // Menggunakan array class bawaan template
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
