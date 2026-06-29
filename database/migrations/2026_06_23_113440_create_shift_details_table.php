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
        Schema::create('shift_details', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel induk (shifts)
            $table->foreignId('shift_id')->constrained('shifts')->onDelete('cascade');

            // Menyimpan nama hari: 'Monday', 'Tuesday', dst.
            $table->string('day_name', 15);

            // Aturan Jam (Bisa nullable jika hari tersebut di-set libur)
            $table->time('start_time')->nullable();    // Jam Masuk (08:00)
            $table->time('end_time')->nullable();      // Jam Pulang (16:00 / 16:30)
            $table->time('late_deadline')->nullable();  // Batas Terlambat (09:00)

            // Penanda apakah hari ini libur untuk shift tersebut
            $table->boolean('is_off')->default(false);

            $table->timestamps();

            // Gabungan shift_id dan day_name harus unik (tidak boleh ada 2 hari Senin di Shift 1)
            $table->unique(['shift_id', 'day_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_details');
    }
};
