<?php
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\LoginActivityController;
use App\Http\Controllers\EmployeeStatusController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\leaveRequestController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\OrganizationStructureController;
use App\Http\Controllers\EmployeeContractController;
use App\Http\Controllers\LeaveAllocationController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ShiftAssignmentController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\AttendanceLogController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceProcessorController;
use App\Http\Controllers\AttendanceMonthlyController;


Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});
Route::get(
    '/dashboard',
    [DashboardController::class, 'index']
)->middleware('auth')
    ->name('dashboard');

Route::get(
    '/dashboard/admin',
    [DashboardController::class, 'adminDashboard']
)->middleware('permission:dashboard.admin')
    ->name('dashboard.admin');

Route::get(
    '/dashboard/employee',
    [DashboardController::class, 'employeeDashboard']
)->middleware('permission:dashboard.employee')
    ->name('dashboard.employee');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::resource('roles', RoleController::class)->middleware('permission:role.view');
    Route::resource('permissions', PermissionController::class);
    Route::resource('users', UserController::class)->middleware('permission:user.view');
    Route::get('/login-activities', [LoginActivityController::class, 'index'])->middleware('permission:activity.view')->name('login-activities.index');
    Route::get('/login-activities/{loginActivity}', [LoginActivityController::class, 'show'])->name('login-activities.show');
    Route::middleware('permission:position.view')->group(function () {
        Route::resource('positions', PositionController::class);
    });
    Route::resource('employee-statuses', EmployeeStatusController::class)->middleware('permission:employee-status.view');
    Route::middleware('permission:activity-log.view')->group(function () {
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    });
    Route::middleware([
        'permission:leave-type.view'
    ])->group(function () {
        Route::resource('leave-types', LeaveTypeController::class);
    });
    Route::middleware('permission:company.view')
        ->group(function () {

            Route::resource(
                'companies',
                CompanyController::class
            );

        });
    Route::middleware('permission:employee.view')
        ->group(function () {

            Route::resource(
                'employees',
                EmployeeController::class
            );

        });

    // --- FITUR ABSENSI: MASTER SHIFT (AMAN DI DALAM AUTH) ---
    // Anda bisa menambahkan middleware permission nanti jika diperlukan, misal: ->middleware('permission:shift.view')
    Route::resource('shifts', ShiftController::class);

    // --- AMAN: DIATAS RESOURCE (AGAR TIDAK DIKIRA SEBAGAI ID) ---
    Route::get('/assignments/available-employees', [ShiftAssignmentController::class, 'getAvailableEmployees'])
        ->name('assignments.available-employees');

    Route::resource('holidays', HolidayController::class);

    // --- BARU RESOURCE-NYA DI BAWAH ---
    Route::resource('assignments', ShiftAssignmentController::class)->only([
        'index',
        'create',
        'store',
        'destroy'
    ]);

    Route::get(
        'leave-requests/approval',
        [LeaveRequestController::class, 'approval']
    )->name('leave-requests.approval');

    Route::post(
        'leave-requests/{leaveRequest}/approve',
        [LeaveRequestController::class, 'approve']
    )->name('leave-requests.approve');

    Route::post(
        'leave-requests/{leaveRequest}/reject',
        [LeaveRequestController::class, 'reject']
    )->name('leave-requests.reject');

    Route::resource(
        'leave-requests',
        LeaveRequestController::class
    );

    Route::middleware(
        'permission:team.view'
    )->group(function () {

        Route::resource(
            'teams',
            TeamController::class
        );

    });

    Route::prefix('teams/{team}')
        ->group(function () {

            Route::get(
                '/members',
                [TeamMemberController::class, 'index']
            )->name('teams.members.index');

            Route::get(
                '/members/create',
                [TeamMemberController::class, 'create']
            )->name('teams.members.create');

            Route::post(
                '/members',
                [TeamMemberController::class, 'store']
            )->name('teams.members.store');

            Route::get(
                '/members/{member}/edit',
                [TeamMemberController::class, 'edit']
            )->name('teams.members.edit');

            Route::put(
                '/members/{member}',
                [TeamMemberController::class, 'update']
            )->name('teams.members.update');

            Route::delete(
                '/members/{member}',
                [TeamMemberController::class, 'destroy']
            )->name('teams.members.destroy');

        });

    Route::get(
        'organization',
        [OrganizationStructureController::class, 'index']
    )->name('organization.index');

    Route::get(
        'attendance-logs/import',
        [AttendanceLogController::class, 'importForm']
    )->name('attendance-logs.import.form');

    Route::post(
        'attendance-logs/import',
        [AttendanceLogController::class, 'import']
    )->name('attendance-logs.import');

    Route::resource(
        'attendance-logs',
        AttendanceLogController::class
    )->except([
                'show'
            ]);

    Route::resource(
        'attendances',
        AttendanceController::class
    )->only([
                'index',
                'show'
            ]);

    Route::middleware([
        'permission:attendance-processor.view'
    ])->group(function () {

        Route::get(
            'attendance-processor',
            [AttendanceProcessorController::class, 'index']
        )->name('attendance-processor.index');

        Route::post(
            'attendance-processor/generate',
            [AttendanceProcessorController::class, 'generate']
        )->name('attendance-processor.generate');

    });
    Route::get(
        'attendance-monthly/employee/{employee}',
        [AttendanceMonthlyController::class, 'show']
    )->name('attendance-monthly.show');

    Route::resource(
        'attendance-monthly',
        AttendanceMonthlyController::class
    )->only([
                'index'
            ]);

    Route::middleware(['auth'])->group(function () {

        Route::post(
            '/leave-requests/{leaveRequest}/approve',
            [LeaveRequestController::class, 'approve']
        )->name('leave-requests.approve');

        Route::post(
            '/leave-requests/{leaveRequest}/reject',
            [LeaveRequestController::class, 'reject']
        )->name('leave-requests.reject');

    });

    Route::resource(
        'employee-contracts',
        EmployeeContractController::class
    );

    Route::prefix('employee-contracts/{employeeContract}')
        ->name('employee-contracts.')
        ->group(function () {

            Route::get(
                '/leave-allocations',
                [LeaveAllocationController::class, 'index']
            )->name('leave-allocations.index');

            Route::get(
                '/leave-allocations/create',
                [LeaveAllocationController::class, 'create']
            )->name('leave-allocations.create');

            Route::post(
                '/leave-allocations',
                [LeaveAllocationController::class, 'store']
            )->name('leave-allocations.store');

            Route::get(
                '/leave-allocations/{leaveAllocation}',
                [LeaveAllocationController::class, 'show']
            )->name('leave-allocations.show');

            Route::get(
                '/leave-allocations/{leaveAllocation}/edit',
                [LeaveAllocationController::class, 'edit']
            )->name('leave-allocations.edit');

            Route::put(
                '/leave-allocations/{leaveAllocation}',
                [LeaveAllocationController::class, 'update']
            )->name('leave-allocations.update');

        });
    // Ini akan otomatis mendaftarkan leave-allocations.store, leave-allocations.destroy, dll.
    Route::resource('leave-allocations', LeaveAllocationController::class);


});



require __DIR__ . '/auth.php';