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
        Schema::create('team_members', function (Blueprint $table) {

            $table->id();

            $table->foreignId('team_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('member_role', [

                'Leader',

                'Co Leader',

                'Member'

            ])->default('Member');

            $table->date('joined_at')->nullable();

            $table->date('left_at')->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique([
                'team_id',
                'employee_id'
            ]);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
