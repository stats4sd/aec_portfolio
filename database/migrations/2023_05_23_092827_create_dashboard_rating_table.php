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
        Schema::create('dashboard_rating', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->decimal('min_rating', 8, 2)->index();
            $table->decimal('max_rating', 8, 2)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_rating');
    }
};
