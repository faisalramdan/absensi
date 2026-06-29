<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'shift_id',
        'date',
        'notes',
        'created_by'
    ];

    // Format field date otomatis menjadi objek Carbon
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Relasi ke Karyawan
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Relasi ke Master Shift
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    /**
     * Relasi ke Pembuat Jadwal
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function user(): BelongsTo
    {
        // KUNCI: Ubah 'user_id' menjadi 'employee_id' agar mengarah ke User yang benar
        return $this->belongsTo(User::class, 'employee_id');
    }
}