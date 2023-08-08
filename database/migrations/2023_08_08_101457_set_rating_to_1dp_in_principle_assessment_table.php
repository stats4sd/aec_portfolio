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
        Schema::table('principle_assessment', function (Blueprint $table) {
            $table->decimal('rating', 8, 1)->nullable()->change();
        });

        Schema::table('additional_criteria_assessment', function (Blueprint $table) {
            $table->decimal('rating', 8, 1)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('1dp_in_principle_assessment', function (Blueprint $table) {
            //
        });
    }
};
