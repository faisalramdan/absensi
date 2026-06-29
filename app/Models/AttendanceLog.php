<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    protected $fillable = [

        'employee_id',

        'date',

        'check_in',

        'check_out',

        'source',

        'notes',

        'created_by',

        'updated_by',

    ];

    public function employee()
    {
        return $this->belongsTo(
            Employee::class
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
