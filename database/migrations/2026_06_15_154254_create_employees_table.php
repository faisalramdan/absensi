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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // Data Utama
            $table->string('employee_no')->unique(); // EMP00001
            $table->string('nik')->unique();
            $table->string('full_name');

            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->enum('gender', [
                'Laki-Laki',
                'Perempuan'
            ])->nullable();

            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();

            $table->string('education')->nullable();

            $table->string('photo')->nullable();

            // KTP
            $table->string('ktp_number')->nullable();
            $table->text('address')->nullable();

            // Kontak Darurat
            $table->string('emergency_name')->nullable();
            $table->string('emergency_relationship')->nullable();
            $table->string('emergency_phone')->nullable();

            // Orang Tua
            $table->string('father_name')->nullable();
            $table->string('father_job')->nullable();
            $table->string('father_phone')->nullable();

            $table->string('mother_name')->nullable();
            $table->string('mother_job')->nullable();
            $table->string('mother_phone')->nullable();

            // Pekerjaan
            $table->foreignId('company_id')->nullable();

            $table->foreignId('position_id')->nullable();

            $table->foreignId('employee_status_id')->nullable();

            $table->foreignId('user_id')->nullable();

            $table->foreignId('role_id')->nullable();

            $table->date('join_date')->nullable();

            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
