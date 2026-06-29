<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveAllocation extends Model
{
    protected $fillable = [

        'employee_contract_id',

        'leave_type_id',

        'allocated_days',

        'used_days',

        'remaining_days',

        'notes',

        'created_by',

        'updated_by',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relationship
    |--------------------------------------------------------------------------
    */

    public function contract()
    {
        return $this->belongsTo(
            EmployeeContract::class,
            'employee_contract_id'
        );
    }

    public function leaveType()
    {
        return $this->belongsTo(
            LeaveType::class
        );
    }

    public function creator()
    {
        return $this->belongsTo(
            Employee::class,
            'created_by'
        );
    }

    public function updater()
    {
        return $this->belongsTo(
            Employee::class,
            'updated_by'
        );
    }
}