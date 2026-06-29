<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shift_assignments', function (Blueprint $table) {
            $table->id();

            // Relasi ke karyawan (sesuaikan dengan nama tabel karyawan Anda, misal: employees atau users)
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');

            // Relasi ke master shift
            $table->foreignId('shift_id')->constrained('shifts')->onDelete('cascade');

            // Tanggal berlakunya shift
            $table->date('date');

            // Keterangan tambahan (opsional) jika ada catatan khusus pada hari tersebut
            $table->string('notes')->nullable();

            // Log pembuat jadwal
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('set null');

            $table->timestamps();

            // Proteksi double assignment: Satu karyawan tidak boleh punya 2 shift di tanggal yang sama
            $table->unique(['employee_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_assignments');
    }
};
