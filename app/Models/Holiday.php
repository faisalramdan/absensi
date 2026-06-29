<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    protected $table = 'holidays';

    protected $fillable = [
        'name',
        'date_actual',
        'date_applied',
        'notes',
        'created_by', // <--- Tambahkan ini
        'updated_by', // <--- Tambahkan ini
    ];

    // Opsional: Cast kolom menjadi format Carbon Date agar mudah dimanipulasi di view jika perlu
    protected $casts = [
        'date_actual' => 'date',
        'date_applied' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by', 'id');
    }
    public function updater()
    {
        return $this->belongsTo(Employee::class, 'updated_by', 'id');
    }
}