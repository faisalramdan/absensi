<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeContract extends Model
{
    protected $fillable = [

        'employee_id',

        'employee_status_id',

        'contract_number',

        'start_date',

        'end_date',

        'file_contract',

        'notes',

        'is_active',

        'created_by',

        'updated_by',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relationship
    |--------------------------------------------------------------------------
    */

    public function employee()
    {
        return $this->belongsTo(
            Employee::class
        );
    }

    public function employeeStatus()
    {
        return $this->belongsTo(
            EmployeeStatus::class
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

    public function leaveAllocations()
    {
        return $this->hasMany(
            LeaveAllocation::class,
            'employee_contract_id'
        );
    }
}