<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $fillable = [

        'employee_id',
        'leave_type_id',

        'start_date',
        'end_date',

        'total_days',

        'reason',
        'attachment',

        'status',

        'approved_by',
        'approved_at',
        'approval_notes',

        'created_by',
        'updated_by',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    // SEBELUMNYA: mengarah ke User::class
// UBAH MENJADI: mengarah ke Employee::class

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function updater()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }

    public function canApprove(Employee $employee): bool
    {
        if ($this->status != 'pending') {
            return false;
        }

        return $this->employee
            ->getApprovers()
            ->contains(function ($approver) use ($employee) {

                return $approver['employee']->id == $employee->id;

            });
    }
}
