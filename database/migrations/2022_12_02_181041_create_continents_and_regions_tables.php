<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('continents', function (Blueprint $table) {
            $table->string('id'); // m49 code
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('regions', function (Blueprint $table) {
            $table->string('id')->primary(); // m49 code
            $table->string('continent_id');
            $table->string('name');
            $table->timestamps();
        });

        // change to use m49 for ids across all levels; use iso-3 codes for countries in new column.
        Schema::table('countries', function(Blueprint $table) {
            $table->string('iso_alpha3');
        });

        Schema::create('continent_project', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id');
            $table->foreignId('continent_id');
            $table->timestamps();
        });

        Schema::create('project_region', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id');
            $table->foreignId('region_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('continents');
    }
};
