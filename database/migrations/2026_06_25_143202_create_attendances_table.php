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
        Schema::create('attendances', function (Blueprint $table) {

            $table->id();

            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('shift_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->date('date');

            /*
            |--------------------------------------------------------------------------
            | Schedule
            |--------------------------------------------------------------------------
            */

            $table->time('scheduled_check_in')
                ->nullable();

            $table->time('scheduled_check_out')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Actual Attendance
            |--------------------------------------------------------------------------
            */

            $table->time('actual_check_in')
                ->nullable();

            $table->time('actual_check_out')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Calculation
            |--------------------------------------------------------------------------
            */

            $table->integer('late_minutes')
                ->default(0);

            $table->integer('early_leave_minutes')
                ->default(0);

            $table->integer('work_minutes')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->string('status');

            $table->foreignId('leave_request_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('leave_type_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Special Case
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_idt')
                ->default(false);

            $table->boolean('is_ipc')
                ->default(false);

            $table->boolean('forgot_check_in')
                ->default(false);

            $table->boolean('forgot_check_out')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            $table->text('remarks')
                ->nullable();

            $table->timestamp('processed_at')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable();

            $table->foreignId('updated_by')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
