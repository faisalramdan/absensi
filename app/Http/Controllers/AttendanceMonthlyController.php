<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Attendance;

class AttendanceMonthlyController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::where('is_active', true)
            ->orderBy('full_name')
            ->get();

        $selectedYear = $request->input(
            'year',
            now()->year
        );

        $selectedMonth = $request->input(
            'month',
            now()->format('m')
        );

        $target = Carbon::create(
            $selectedYear,
            $selectedMonth,
            1
        );

        $startDate = $request->input(
            'start_date',
            $target->copy()->subMonth()->day(26)->format('Y-m-d')
        );

        $endDate = $request->input(
            'end_date',
            $target->copy()->day(25)->format('Y-m-d')
        );

        $query = Attendance::with([
            'employee',
            'leaveType'
        ])
            ->join(
                'employees',
                'employees.id',
                '=',
                'attendances.employee_id'
            )
            ->where(
                'employees.is_active',
                true
            )
            ->whereBetween(
                'attendances.date',
                [
                    $startDate,
                    $endDate
                ]
            )
            ->orderBy(
                'employees.full_name',
                'asc'
            )
            ->select(
                'attendances.*'
            );

        if ($request->filled('employee_id')) {

            $query->where(
                'attendances.employee_id',
                $request->employee_id
            );

        }

        $rows = $query->get();

        $summary = $rows
            ->groupBy('employee_id')
            ->map(function ($items) {

                $employee = $items->first()->employee;

                return [

                    'employee' => $employee,

                    'present' => $items
                        ->where('status', 'present')
                        ->count(),

                    'alpha' => $items
                        ->where('status', 'alpha')
                        ->count(),

                    'sick' => $items
                        ->filter(function ($row) {

                            return $row->status == 'leave'
                                && optional($row->leaveType)->tag == 'sick';

                        })
                        ->count(),

                    'permission' => $items
                        ->filter(function ($row) {

                            return $row->status == 'leave'
                                && optional($row->leaveType)->tag == 'izin';

                        })
                        ->count(),

                    'annual_leave' => $items
                        ->filter(function ($row) {

                            return $row->status == 'leave'
                                && optional($row->leaveType)->tag == 'cuti';

                        })
                        ->count(),

                    'wfa' => $items
                        ->filter(function ($row) {

                            return $row->status == 'leave'
                                && optional($row->leaveType)->tag == 'wfa';

                        })
                        ->count(),

                    'holiday' => $items
                        ->where('status', 'holiday')
                        ->count(),

                    'off' => $items
                        ->where('status', 'off')
                        ->count(),

                    'late' => $items
                        ->where('late_minutes', '>', 0)
                        ->count(),

                    'late_minutes' => $items
                        ->sum('late_minutes'),

                    'early_leave' => $items
                        ->where('early_leave_minutes', '>', 0)
                        ->count(),

                    'early_leave_minutes' => $items
                        ->sum('early_leave_minutes'),

                    'forgot_in' => $items
                        ->where('forgot_check_in', true)
                        ->count(),

                    'forgot_out' => $items
                        ->where('forgot_check_out', true)
                        ->count(),

                    'ipc' => $items
                        ->where('is_ipc', true)
                        ->count(),

                    'idt' => $items
                        ->where('is_idt', true)
                        ->count(),

                    'work_minutes' => $items
                        ->sum('work_minutes'),

                    'kurang_hk' => $items
                        ->where('status', 'alpha')
                        ->count(),

                    'kurang_jam' => max(
                        0,
                        ($items
                            ->where('status', 'present')
                            ->count() * 480)

                        -
                        $items->sum('work_minutes')

                    ),

                ];

            })

            ->sortBy(function ($item) {
                return $item['employee']->full_name;
            }, SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        return view(
            'attendance-monthly.index',
            compact(
                'summary',
                'employees',
                'selectedYear',
                'selectedMonth',
                'startDate',
                'endDate'
            )
        );
    }

    public function show($id)
    {

    }
}