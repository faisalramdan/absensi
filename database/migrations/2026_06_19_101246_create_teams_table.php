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
        Schema::create('teams', function (Blueprint $table) {

            $table->id();


            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('teams')
                ->nullOnDelete();

            $table->text('description')
                ->nullable();

            $table->integer('sort_order')
                ->default(0);

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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
