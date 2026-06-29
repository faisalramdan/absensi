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
        Schema::table('holidays', function (Blueprint $table) {
            // Menambahkan field audit trail berdasarkan employee_id
            // Digunakan nullable() agar data lama yang sudah ada tidak error saat proses migrate
            $table->unsignedBigInteger('created_by')->nullable()->after('notes');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');

            // Opsional: Jika Anda ingin menambahkan Foreign Key Constraints ke tabel employees
            // $table->foreign('created_by')->references('id')->on('employees')->onDelete('set null');
            // $table->foreign('updated_by')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('holidays', function (Blueprint $table) {
            // Drop foreign key terlebih dahulu jika sebelumnya diaktifkan
            // $table->dropForeign(['created_by']);
            // $table->dropForeign(['updated_by']);

            $table->dropColumn(['created_by', 'updated_by']);
        });
    }
};
