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
        Schema::table('leave_requests', function (Blueprint $table) {
            // 1. Drop foreign key lama yang mengarah ke tabel users
            $table->dropForeign('leave_requests_approved_by_foreign');

            // 2. Ubah constraint agar mengarah ke tabel employees
            $table->foreign('approved_by')
                ->references('id')
                ->on('employees')
                ->onDelete('set null'); // Aman jika data employee terhapus
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            // Mengembalikan ke state semula jika di-rollback
            $table->dropForeign(['approved_by']);
            $table->foreign('approved_by')
                ->references('id')
                ->on('users');
        });
    }
};
