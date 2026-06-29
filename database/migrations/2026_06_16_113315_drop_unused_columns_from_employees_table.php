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
            $table->dropColumn([
                'employee_no',
                'emergency_name',
                'emergency_relationship',
                'emergency_phone',
                'father_name',
                'father_job',
                'father_phone',
                'mother_name',
                'mother_job',
                'mother_phone',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('employee_no')->nullable();

            $table->string('emergency_name')->nullable();
            $table->string('emergency_relationship')->nullable();
            $table->string('emergency_phone')->nullable();

            $table->string('father_name')->nullable();
            $table->string('father_job')->nullable();
            $table->string('father_phone')->nullable();

            $table->string('mother_name')->nullable();
            $table->string('mother_job')->nullable();
            $table->string('mother_phone')->nullable();
        });
    }
};
