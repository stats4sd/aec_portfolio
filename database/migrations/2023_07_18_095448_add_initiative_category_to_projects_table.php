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
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('initiative_category_id')
                ->after('code')
                ->nullable()
                ->constrained('initiative_categories')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->string('initiative_category_other')
                ->after('initiative_category_id')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('initiative_category_id');
            $table->dropColumn('initiative_category_other');
        });
    }
};
