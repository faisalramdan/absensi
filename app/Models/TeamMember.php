<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $fillable = [

        'team_id',

        'employee_id',

        'member_role',

        'joined_at',

        'left_at',

        'is_active',

        'created_by',

        'updated_by',

    ];

    public function team()
    {
        return $this->belongsTo(
            Team::class
        );
    }

    public function employee()
    {
        return $this->belongsTo(
            Employee::class
        );
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
}