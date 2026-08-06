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
        Schema::table('attendances', function (Blueprint $table) {
            // Menambahkan kolom is_khs dengan tipe boolean (default false/0)
            // Diletakkan setelah kolom is_ipc agar posisinya berurutan rapi
            $table->boolean('is_khs')->default(false)->after('is_ipc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Perintah untuk rollback (menghapus kolom jika migration di-rollback)
            $table->dropColumn('is_khs');
        });
    }
};
