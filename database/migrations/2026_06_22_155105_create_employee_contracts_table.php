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
        Schema::create('employee_contracts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('employee_status_id')
                ->constrained('employee_statuses');

            $table->string('contract_number')->nullable();

            $table->date('start_date');

            $table->date('end_date');

            $table->string('file_contract')->nullable();

            $table->text('notes')->nullable();

            $table->boolean('is_active')
                ->default(true);

            // Ubah 'users' menjadi 'employees'
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('employees') // <--- Diarahkan ke tabel employees
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('employees') // <--- Diarahkan ke tabel employees
                ->nullOnDelete();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_contracts');
    }
};
