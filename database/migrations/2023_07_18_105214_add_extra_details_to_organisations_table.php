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
        Schema::table('organisations', function (Blueprint $table) {
            $table->foreignId('institution_type_id')
                ->nullable()
                ->constrained('institution_types')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->string('institution_type_other')->nullable();

            $table->string('hq_country')
                ->nullable()
                ->constrained('countries')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->string('geographic_reach')->nullable();
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organisations', function (Blueprint $table) {
            //
        });
    }
};
