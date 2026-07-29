<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'code',
        'name',
        'address',
        'phone',
        'email',
        'logo',
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

    public function teams()
    {
        return $this->hasMany(
            Team::class
        );
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'company_id', 'id');

        // Catatan: Jika nama foreign key di tabel employees Anda berbeda 
        // (misalnya bukan 'company_id'), silakan sesuaikan parameter di atas.
    }
}
