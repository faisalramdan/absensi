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
        $employees = Employee::where('is_active', true)
            ->orderBy('full_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Periode Default (26 - 25)
        |--------------------------------------------------------------------------
        */

        $selectedYear = $request->input(
            'year',
            date('Y')
        );

        $selectedMonth = $request->input(
            'month',
            date('m')
        );

        $targetDate = Carbon::createFromDate(
            $selectedYear,
            $selectedMonth,
            1
        );

        $defaultStart = $targetDate
            ->copy()
            ->subMonth()
            ->day(26)
            ->format('Y-m-d');

        $defaultEnd = $targetDate
            ->copy()
            ->day(25)
            ->format('Y-m-d');

        /*
        |--------------------------------------------------------------------------
        | Tanggal Aktif
        |--------------------------------------------------------------------------
        */

        $startDate = $request->input(
            'start_date',
            $defaultStart
        );

        $endDate = $request->input(
            'end_date',
            $defaultEnd
        );

        /*
        |--------------------------------------------------------------------------
        | Query
        |--------------------------------------------------------------------------
        */

        $query = Attendance::with([
            'employee',
            'shift',
            'leaveType'
        ]);

        $query->whereBetween('date', [
            $startDate,
            $endDate
        ]);

        if ($request->filled('employee_id')) {

            $query->where(
                'employee_id',
                $request->employee_id
            );
        }

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $summary = [

            'present' => (clone $query)
                ->where('status', 'present')
                ->count(),

            'late' => (clone $query)
                ->where('late_minutes', '>', 0)
                ->count(),

            'early_leave' => (clone $query)
                ->where('early_leave_minutes', '>', 0)
                ->count(),

            'forgot_check_in' => (clone $query)
                ->where('forgot_check_in', true)
                ->count(),

            'forgot_check_out' => (clone $query)
                ->where('forgot_check_out', true)
                ->count(),

            'alpha' => (clone $query)
                ->where('status', 'alpha')
                ->count(),

            'leave' => (clone $query)
                ->where('status', 'leave')
                ->count(),

            'holiday' => (clone $query)
                ->where('status', 'holiday')
                ->count(),

            'off' => (clone $query)
                ->where('status', 'off')
                ->count(),

            'total_work_minutes' => (clone $query)
                ->sum('work_minutes'),

            'total_late_minutes' => (clone $query)
                ->sum('late_minutes'),

        ];

        /*
        |--------------------------------------------------------------------------
        | Hari Kerja
        |--------------------------------------------------------------------------
        */

        $workingDays = 0;

        $sundayCount = 0;

        $holidayCount = 0;

        $calendarDays = Carbon::parse($startDate)
            ->diffInDays(Carbon::parse($endDate)) + 1;

        $current = Carbon::parse($startDate);

        while ($current->lte(Carbon::parse($endDate))) {

            if ($current->dayOfWeek == Carbon::SUNDAY) {

                $sundayCount++;

            } else {

                $holiday = Holiday::whereDate(
                    'date_applied',
                    $current
                )->exists();

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
        | Attendance Table
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