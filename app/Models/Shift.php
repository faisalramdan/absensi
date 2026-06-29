<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'created_by',
        'updated_by'
    ];

    /**
     * Hubungan ke data detail jam harian (Senin - Minggu)
     */
    public function details(): HasMany
    {
        return $this->hasMany(ShiftDetail::class, 'shift_id');
    }

    /**
     * Melacak pembuat shift dari data Employee
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    /**
     * Melacak pengubah shift dari data Employee
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'updated_by');
    }
}