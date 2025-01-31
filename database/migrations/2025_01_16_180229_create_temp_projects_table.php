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

        // We are going to add a workflow to show the uploaded project data and validation result in front end.
        // User can find project record easily, make corrections in excel file and then import again.
        //
        // This table is created for storing temporary projects data in the uploaded excel file.
        //
        // When all data are correct in excel file, user can import the excel file as real projects records.
        // At this point, all related temp_projects records can be removed.
        //
        // Consider the purpose of this table, below design are applied:
        // 1. All columns are either string or text (user can key in anything in any numeric field)
        // 2. All columns are nullable (user can leave any field empty)
        // 3. Records are related to a project import, which links to a user
        // 4. Related records will be removed when user re-upload the excel file or user finialised to save the excel file

        Schema::create('temp_projects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('temp_project_import_id')
                ->references('id')
                ->on('temp_project_imports')
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

            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->string('currency')->nullable();
            $table->string('exchange_rate')->nullable();
            $table->string('exchange_rate_eur')->nullable();
            $table->string('budget')->nullable();
            $table->string('uses_only_own_funds')->nullable();
            $table->string('main_recipient')->nullable();
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->string('geographic_reach')->nullable();
            $table->text('continents')->nullable();
            $table->text('regions')->nullable();
            $table->text('countries')->nullable();
            $table->boolean('valid')->default(0);
            $table->text('validation_result')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_projects');
    }
};
