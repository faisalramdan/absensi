<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_id',
        'day_name',
        'start_time',
        'end_time',
        'late_deadline',
        'is_off'
    ];

    /**
     * Memastikan data bertipe boolean otomatis dikonversi oleh Laravel
     */
    protected $casts = [
        'is_off' => 'boolean',
    ];

    /**
     * Hubungan balik ke model Master Shift
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }
}
