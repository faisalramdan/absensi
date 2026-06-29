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
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->date('date_actual');    // Tanggal merah KALENDER ASLI (misal: 2026-05-29 hari Jumat)
            $table->date('date_applied');   // Tanggal LIBUR YANG BERLAKU (misal: 2026-05-30 hari Sabtu)
            $table->string('name');         // Nama Libur (misal: Cuti Bersama - Digeser ke Sabtu)
            $table->string('notes')->nullable(); // <-- TAMBAHKAN CATATAN INI (Boleh Kosong)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
