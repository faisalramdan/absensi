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
        // 1. Hapus foreign key lama jika ada (Aman untuk PostgreSQL)
        $this->dropConstraintIfExists('leave_requests', 'leave_requests_created_by_foreign');
        $this->dropConstraintIfExists('leave_requests', 'leave_requests_updated_by_foreign');

        // 2. Buat Foreign Key baru mengarah ke tabel employees
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->foreign('created_by')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade');

            $table->foreign('updated_by')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        $this->dropConstraintIfExists('leave_requests', 'leave_requests_created_by_foreign');
        $this->dropConstraintIfExists('leave_requests', 'leave_requests_updated_by_foreign');

        Schema::table('leave_requests', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Fungsi pembantu untuk memeriksa & menghapus constraint dengan aman di PostgreSQL
     */
    private function dropConstraintIfExists(string $table, string $constraint): void
    {
        $exists = DB::selectOne("
            SELECT 1 
            FROM information_schema.table_constraints 
            WHERE table_name = :table AND constraint_name = :constraint
        ", ['table' => $table, 'constraint' => $constraint]);

        if ($exists) {
            DB::statement("ALTER TABLE \"{$table}\" DROP CONSTRAINT \"{$constraint}\"");
        }
    }
};
