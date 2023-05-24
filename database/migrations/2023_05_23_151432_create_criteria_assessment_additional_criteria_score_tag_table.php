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
        Schema::create('additional_criteria_assessment_additional_criteria_score_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('additional_criteria_assessment_id');
            $table->foreign('additional_criteria_assessment_id', 'ca_acst_ca_id')
                ->on('additional_criteria_assessment')
                ->references('id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('additional_criteria_score_tag_id');
            $table->foreign('additional_criteria_score_tag_id', 'ca_acst_acst_id')
                ->on('additional_criteria_score_tags')
                ->references('id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('assessment_id');
            $table->foreign('assessment_id', 'ca_acst_assessment_id')
                ->on('assessments')
                ->references('id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criteria_assessment_additional_criteria_score_tag');
    }
};
