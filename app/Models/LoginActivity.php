<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginActivity extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'event',
        'logged_at',
    ];
    protected $casts = [
        'logged_at' => 'datetime',
    ];
}
