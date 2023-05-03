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
        Schema::create('assessment_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')
                ->constrained();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('link')->nullable();
            $table->integer('number');
            $table->text('rating_two');
            $table->text('rating_one');
            $table->text('rating_zero');
            $table->text('rating_na');
            $table->boolean('can_be_na')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_criteria');
    }
};
