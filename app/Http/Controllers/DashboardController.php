<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Models\LoginActivity;

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

        // 1. Ambil kontrak yang sedang aktif langsung via Model Contract
        $activeContract = \App\Models\EmployeeContract::where('employee_id', $employee->id)
            ->where('is_active', true) // Jika di PostgreSQL menggunakan string 't', ubah menjadi 't'
            ->latest()
            ->first();

        // 2. Ambil data alokasi kuota berdasarkan kontrak aktif tersebut
        // Kita load juga relasi leaveType agar nama jenis cutinya bisa muncul di tabel
        $leaveAllocations = collect(); // Default berupa collection kosong jika kontrak tidak ada

        if ($activeContract) {
            $leaveAllocations = \App\Models\LeaveAllocation::with('leaveType')
                ->where('employee_contract_id', $activeContract->id)
                ->get();
        }

        // 3. Count data pengajuan cuti (tetap dipertahankan jika sewaktu-waktu ingin dipakai)
        $pendingLeaves = $employee->leaveRequests()->where('status', 'pending')->count();
        $approvedLeaves = $employee->leaveRequests()->where('status', 'approved')->count();
        $rejectedLeaves = $employee->leaveRequests()->where('status', 'rejected')->count();

        return view('dashboard.employee', compact(
            'employee',
            'activeContract',
            'leaveAllocations', // <--- Variabel baru yang dibutuhkan oleh tabel di Blade
            'pendingLeaves',
            'approvedLeaves',
            'rejectedLeaves'
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
}
