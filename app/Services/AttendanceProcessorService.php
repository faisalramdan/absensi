<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Holiday;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\AttendanceLog;
use App\Models\ShiftAssignment;

class AttendanceProcessorService
{
    public function process(
        int $employeeId,
        string $date
    ) {

        $date = Carbon::parse($date);

        /*
        |--------------------------------------------------------------------------
        | Holiday
        |--------------------------------------------------------------------------
        */

        $holiday = Holiday::whereDate(
            'date_applied',
            $date
        )->first();

        if ($holiday) {

            return Attendance::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'date' => $date->format('Y-m-d')
                ],
                [
                    'status' => 'holiday',

                    'remarks' => $holiday->name,

                    'source' => 'generated',

                    'processed_at' => now()
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Leave Request
        |--------------------------------------------------------------------------
        */

        $leaveRequest = LeaveRequest::with(
            'leaveType'
        )
            ->where(
                'employee_id',
                $employeeId
            )
            ->where(
                'status',
                'approved'
            )
            ->whereDate(
                'start_date',
                '<=',
                $date
            )
            ->whereDate(
                'end_date',
                '>=',
                $date
            )
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Leave Normal
        |--------------------------------------------------------------------------
        */

        if ($leaveRequest) {

            $leaveCode =
                $leaveRequest
                    ->leaveType
                        ?->code;

            if (
                !in_array(
                    $leaveCode,
                    [
                        'I-IDT',
                        'I-IPC'
                    ]
                )
            ) {

                $status =
                    $leaveCode == 'I-SKT'
                    ? 'sick'
                    : 'leave';

                return Attendance::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'date' => $date->format('Y-m-d')
                    ],
                    [
                        'status' => $status,

                        'leave_request_id' =>
                            $leaveRequest->id,

                        'leave_type_id' =>
                            $leaveRequest->leave_type_id,

                        'source' => 'generated',

                        'processed_at' => now()
                    ]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Shift Assignment
        |--------------------------------------------------------------------------
        */

        $assignment = ShiftAssignment::with(
            'shift.details'
        )
            ->where(
                'employee_id',
                $employeeId
            )
            ->whereDate(
                'date',
                $date
            )
            ->first();

        if (!$assignment) {

            return Attendance::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'date' => $date->format('Y-m-d')
                ],
                [
                    'status' => 'off',

                    'source' => 'generated',

                    'processed_at' => now()
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Shift Detail
        |--------------------------------------------------------------------------
        */

        $dayName = $date->format('l');

        $shiftDetail = $assignment
            ->shift
            ->details
            ->where(
                'day_name',
                $dayName
            )
            ->first();

        if (!$shiftDetail) {

            return Attendance::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'date' => $date->format('Y-m-d')
                ],
                [
                    'status' => 'off',

                    'source' => 'generated',

                    'processed_at' => now()
                ]
            );
        }

        if ($shiftDetail->is_off) {

            return Attendance::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'date' => $date->format('Y-m-d')
                ],
                [
                    'status' => 'off',

                    'source' => 'generated',

                    'processed_at' => now()
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Attendance Log
        |--------------------------------------------------------------------------
        */

        $log = AttendanceLog::where(
            'employee_id',
            $employeeId
        )
            ->whereDate(
                'date',
                $date
            )
            ->first();

        if (!$log) {

            return Attendance::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'date' => $date->format('Y-m-d')
                ],
                [
                    'shift_id' => $assignment->shift_id,

                    'status' => 'alpha',

                    'source' => 'generated',

                    'processed_at' => now()
                ]
            );
        }

        return $this->processPresent(
            $assignment,
            $shiftDetail,
            $log,
            $date,
            $leaveRequest
        );
    }

    private function processPresent(
        $assignment,
        $shiftDetail,
        $log,
        $date,
        $leaveRequest = null
    ) {

        /*
        |--------------------------------------------------------------------------
        | Required Work Minutes
        |--------------------------------------------------------------------------
        */

        $shiftStart = Carbon::parse(
            $date->format('Y-m-d') . ' ' . $shiftDetail->start_time
        );

        $shiftEnd = Carbon::parse(
            $date->format('Y-m-d') . ' ' . $shiftDetail->end_time
        );

        $requiredWorkMinutes =
            $shiftStart->diffInMinutes(
                $shiftEnd
            );

        /*
        |--------------------------------------------------------------------------
        | Actual Time
        |--------------------------------------------------------------------------
        */

        $actualIn = null;
        $actualOut = null;

        if (!empty($log->check_in)) {

            $actualIn = Carbon::parse(
                $date->format('Y-m-d')
                . ' '
                . $log->check_in
            );
        }

        if (!empty($log->check_out)) {

            $actualOut = Carbon::parse(
                $date->format('Y-m-d')
                . ' '
                . $log->check_out
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Forgot Attendance
        |--------------------------------------------------------------------------
        */

        $forgotCheckIn =
            empty($log->check_in);

        $forgotCheckOut =
            empty($log->check_out);

        /*
        |--------------------------------------------------------------------------
        | Late Deadline
        |--------------------------------------------------------------------------
        */

        $lateDeadline = Carbon::parse(
            $date->format('Y-m-d')
            . ' '
            . $shiftDetail->late_deadline
        );

        /*
        |--------------------------------------------------------------------------
        | Late Minutes
        |--------------------------------------------------------------------------
        */

        $lateMinutes = 0;

        if (
            $actualIn &&
            $actualIn->gt($lateDeadline)
        ) {

            $lateMinutes =
                $lateDeadline->diffInMinutes(
                    $actualIn
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Required Check Out
        |--------------------------------------------------------------------------
        */

        $requiredCheckOut = null;

        if ($actualIn) {

            $requiredCheckOut =
                $actualIn
                    ->copy()
                    ->addMinutes(
                        $requiredWorkMinutes
                    );
        }

        /*
        |--------------------------------------------------------------------------
        | Work Minutes
        |--------------------------------------------------------------------------
        */

        $workMinutes = 0;

        if (
            $actualIn &&
            $actualOut
        ) {

            $workMinutes =
                $actualIn->diffInMinutes(
                    $actualOut
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Lupa Check Out
        |--------------------------------------------------------------------------
        */

        if (
            $actualIn &&
            !$actualOut
        ) {

            $workMinutes =
                $requiredWorkMinutes;
        }

        /*
        |--------------------------------------------------------------------------
        | Lupa Check In
        |--------------------------------------------------------------------------
        */

        if (
            !$actualIn &&
            $actualOut
        ) {

            $workMinutes =
                $requiredWorkMinutes;
        }

        /*
        |--------------------------------------------------------------------------
        | Early Leave
        |--------------------------------------------------------------------------
        */

        $earlyLeaveMinutes = 0;

        if (
            $requiredCheckOut &&
            $actualOut &&
            $actualOut->lt(
                $requiredCheckOut
            )
        ) {

            $earlyLeaveMinutes =
                $actualOut->diffInMinutes(
                    $requiredCheckOut
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Short Work
        |--------------------------------------------------------------------------
        */

        $shortWorkMinutes =
            max(
                0,
                $requiredWorkMinutes -
                $workMinutes
            );

        /*
        |--------------------------------------------------------------------------
        | IDT / IPC
        |--------------------------------------------------------------------------
        */

        $isIdt = false;
        $isIpc = false;

        if ($leaveRequest) {

            $code =
                $leaveRequest
                    ->leaveType
                        ?->code;

            $isIdt =
                $code === 'I-IDT';

            $isIpc =
                $code === 'I-IPC';
        }

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        $status = 'present';

        /*
        |--------------------------------------------------------------------------
        | Save
        |--------------------------------------------------------------------------
        */

        return Attendance::updateOrCreate(
            [
                'employee_id' =>
                    $assignment->employee_id,

                'date' =>
                    $date->format('Y-m-d')
            ],
            [
                'shift_id' =>
                    $assignment->shift_id,

                'scheduled_check_in' =>
                    $shiftDetail->start_time,

                'scheduled_check_out' =>
                    $requiredCheckOut
                    ? $requiredCheckOut->format('H:i:s')
                    : null,

                'actual_check_in' =>
                    $log->check_in,

                'actual_check_out' =>
                    $log->check_out,

                'late_minutes' =>
                    $lateMinutes,

                'early_leave_minutes' =>
                    $earlyLeaveMinutes,

                'work_minutes' =>
                    $workMinutes,

                'short_work_minutes' =>
                    $shortWorkMinutes,

                'forgot_check_in' =>
                    $forgotCheckIn,

                'forgot_check_out' =>
                    $forgotCheckOut,

                'is_idt' =>
                    $isIdt,

                'is_ipc' =>
                    $isIpc,

                'leave_request_id' =>
                    $leaveRequest?->id,

                'leave_type_id' =>
                    $leaveRequest?->leave_type_id,

                'status' =>
                    $status,
                'source' => $log->source,

                'notes' => $log->notes,

                'processed_at' =>
                    now()
            ]
        );
    }
}