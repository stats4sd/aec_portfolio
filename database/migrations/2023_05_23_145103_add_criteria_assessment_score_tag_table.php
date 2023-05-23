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
        Schema::create('criteria_assessment_score_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('criteria_assessment_id')->constrained('criteria_assessment');
            $table->foreignId('score_tag_id')->constrained();
            $table->foreignId('assessment_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
