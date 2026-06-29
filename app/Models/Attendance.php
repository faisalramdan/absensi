<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [

        'employee_id',

        'shift_id',

        'date',

        'scheduled_check_in',
        'scheduled_check_out',

        'actual_check_in',
        'actual_check_out',
        'source',

        'late_minutes',
        'early_leave_minutes',
        'work_minutes',
        'short_work_minutes',

        'status',

        'leave_request_id',
        'leave_type_id',

        'is_idt',
        'is_ipc',

        'forgot_check_in',
        'forgot_check_out',

        'remarks',
        'notes',

        'processed_at',

        'created_by',
        'updated_by',

    ];

    public function employee()
    {
        return $this->belongsTo(
            Employee::class
        );
    }

    public function shift()
    {
        return $this->belongsTo(
            Shift::class
        );
    }

    public function leaveRequest()
    {
        return $this->belongsTo(
            LeaveRequest::class
        );
    }

    public function leaveType()
    {
        return $this->belongsTo(
            LeaveType::class
        );
    }
}
