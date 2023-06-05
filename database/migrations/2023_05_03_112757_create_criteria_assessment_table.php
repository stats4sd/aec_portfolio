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
        Schema::create('additional_criteria_assessment', function (Blueprint $table) {
            $table->id();

            $table->foreignId('assessment_id')
                ->references('id')
                ->on('assessments')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('additional_criteria_id')
                ->references('id')
                ->on('additional_criteria')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->decimal('rating', 3, 2)->nullable();
            $table->text('rating_comment')->nullable();
            $table->boolean('is_na')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('additional_criteria_assessment');
    }
};
