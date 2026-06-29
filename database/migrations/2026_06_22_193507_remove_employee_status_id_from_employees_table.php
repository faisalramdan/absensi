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
        Schema::table('employees', function (Blueprint $table) {
            // Hapus atau beri komentar pada dropForeign karena constraint-nya tidak terdeteksi
            // $table->dropForeign(['employee_status_id']); 

            // Langsung hapus kolomnya saja
            $table->dropColumn('employee_status_id');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Kembalikan jika diperlukan rollback
            $table->foreignId('employee_status_id')->nullable()->constrained('employee_statuses');
        });
    }
};
