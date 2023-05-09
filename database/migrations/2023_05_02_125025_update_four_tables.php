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
            $table->renameColumn('project_id', 'assessment_id');
        });

        Schema::table('principle_project', function (Blueprint $table) {
            $table->renameColumn('project_id', 'assessment_id');
        });

        Schema::rename('project_red_line', 'assessment_red_line');

        Schema::rename('principle_project', 'principle_assessment');


        // Schema::table('principle_project_score_tag', function (Blueprint $table) {
        //     $table->renameColumn('principle_project_id', 'principle_assessment_id');
        //     $table->renameColumn('project_id', 'assessment_id');
        // });

        // Schema::table('custom_score_tags', function (Blueprint $table) {
        //     $table->renameColumn('principle_project_id', 'principle_assessment_id');
        //     $table->renameColumn('project_id', 'assessment_id');
        // });

        // Schema::rename('principle_project_score_tag', 'principle_assessment_score_tag');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::rename('assessment_red_line', 'project_red_line');

        Schema::rename('principle_assessment', 'principle_project');


        Schema::table('project_red_line', function (Blueprint $table) {
            $table->dropColumn('assessment_id');
        });

        Schema::table('principle_project', function (Blueprint $table) {
            $table->dropColumn('assessment_id');
        });


        // Schema::rename('principle_assessment_score_tag', 'principle_project_score_tag');

        // Schema::table('principle_project_score_tag', function (Blueprint $table) {
        //     $table->renameColumn('principle_assessment_id', 'principle_project_id');
        //     $table->renameColumn('assessment_id', 'project_id');
        // });

        // Schema::table('custom_score_tags', function (Blueprint $table) {
        //     $table->renameColumn('principle_assessment_id', 'principle_project_id');
        //     $table->renameColumn('assessment_id', 'project_id');
        // });

    }
};
