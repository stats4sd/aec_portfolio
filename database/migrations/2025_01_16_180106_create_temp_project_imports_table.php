<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('temp_project_imports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('organisation_id')
                ->references('id')
                ->on('organisations')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('portfolio_id')
                ->references('id')
                ->on('portfolios')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('portfolio_name');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_project_imports');
    }
};
