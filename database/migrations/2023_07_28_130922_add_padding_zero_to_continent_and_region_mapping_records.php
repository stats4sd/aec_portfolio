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
        // add padding zero to continent_id for existing mapping records
        $sql01 = 'UPDATE continent_project SET continent_id = LPAD(continent_id, 3, \'0\');';
        logger($sql01);
        $result01 = DB::statement($sql01);

        // add padding zero to region_id for existing mapping records
        $sql02 = 'UPDATE project_region SET region_id = LPAD(region_id, 3, \'0\');';
        logger($sql02);
        $result02 = DB::statement($sql02);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
