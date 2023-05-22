<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // for data integrity and avoid SQL error , need to remove all records of below tables before adding ON DELETE constraint

        // add ON DELETE for assessment related tables
        DB::statement("ALTER TABLE assessment_red_line ADD CONSTRAINT fk_arl_assessments FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE;");
        DB::statement("ALTER TABLE principle_assessment ADD CONSTRAINT fk_pa_assessments FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE;");

        // TODO: table name to be confirmed for below two tables
        // DB::statement("ALTER TABLE custom_score_tags ADD CONSTRAINT fk_cst_assessments FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE;");
        // DB::statement("ALTER TABLE principle_project_score_tag ADD CONSTRAINT fk_ppst_assessments FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE;");

        // TODO: add ON DELETE for custom principles table (table to be added)

        // add ON DELETE for project related tables
        DB::statement("ALTER TABLE assessments ADD CONSTRAINT fk_a_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE;");
        DB::statement("ALTER TABLE continent_project ADD CONSTRAINT fk_conp_projects FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE;");
        DB::statement("ALTER TABLE country_project ADD CONSTRAINT fk_coup_projects FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE;");
        DB::statement("ALTER TABLE project_region ADD CONSTRAINT fk_pr_projects FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE;");

        // add ON DELETE for organisation related tables
        DB::statement("ALTER TABLE portfolios ADD CONSTRAINT fk_port_organisations FOREIGN KEY (organisation_id) REFERENCES organisations(id) ON DELETE CASCADE;");
        DB::statement("ALTER TABLE projects ADD CONSTRAINT fk_pro_organisations FOREIGN KEY (organisation_id) REFERENCES organisations(id) ON DELETE CASCADE;");
        DB::statement("ALTER TABLE organisation_members ADD CONSTRAINT fk_om_organisations FOREIGN KEY (organisation_id) REFERENCES organisations(id) ON DELETE CASCADE;");

        // Question: Is there any way to delete master records (users) when child records (organisaiton_members) are deleted...?
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
