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
        Schema::table('additional_criteria_score_tags', function (Blueprint $table) {
            $table->dropForeign('additional_criteria_score_tags_additional_criteria_id_foreign');

            $table->foreign('additional_criteria_id')
                ->on('additional_criteria')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('additional_criteria_score_tags', function (Blueprint $table) {
            //
        });
    }
};
