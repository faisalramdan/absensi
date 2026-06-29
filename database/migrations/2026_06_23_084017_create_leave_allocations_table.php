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
        Schema::create('leave_allocations', function (Blueprint $table) {

            $table->id();

            $table->foreignId('employee_contract_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('leave_type_id')
                ->constrained();

            $table->decimal(
                'allocated_days',
                5,
                2
            );

            $table->decimal(
                'used_days',
                5,
                2
            )->default(0);

            $table->decimal(
                'remaining_days',
                5,
                2
            );

            $table->text('notes')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_allocations');
    }
};
