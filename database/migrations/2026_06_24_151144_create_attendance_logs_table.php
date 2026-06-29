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
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('date');

            $table->time('check_in')
                ->nullable();

            $table->time('check_out')
                ->nullable();

            $table->enum('source', [

                'import_excel',

                'manual',

            ])->default('manual');

            $table->text('notes')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained(
                    'employees'
                );

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained(
                    'employees'
                );

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
