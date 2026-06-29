<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeStatus extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
