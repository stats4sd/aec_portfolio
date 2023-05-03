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
        Schema::table('project_red_line', function (Blueprint $table) {
            $table->foreignId('assessment_id')->after('project_id');
        });

        Schema::table('principle_project', function (Blueprint $table) {
            $table->foreignId('assessment_id')->after('project_id');
        });

        Schema::table('principle_project_score_tag', function (Blueprint $table) {
            $table->foreignId('principle_assessment_id')->after('principle_project_id');
            $table->foreignId('assessment_id')->after('project_id');
        });

        Schema::table('custom_score_tags', function (Blueprint $table) {
            $table->foreignId('assessment_id')->after('project_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_red_line', function (Blueprint $table) {
            $table->dropColumn('assessment_id');
        });

        Schema::table('principle_project', function (Blueprint $table) {
            $table->dropColumn('assessment_id');
        });

        Schema::table('principle_project_score_tag', function (Blueprint $table) {
            $table->dropColumn('principle_assessment_id');
            $table->dropColumn('assessment_id');
        });

        Schema::table('custom_score_tags', function (Blueprint $table) {
            $table->dropColumn('assessment_id');
        });

    }
};
