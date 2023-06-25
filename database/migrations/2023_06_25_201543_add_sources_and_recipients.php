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
        Schema::create('funding_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained();
            $table->foreignId('institution_id')->nullable()->comment('If a specific institution was selected, this is the id');
            $table->string('source')->comment('If a specific institution was selected, the name is also stored here');
            $table->unsignedBigInteger('amount');
            $table->timestamps();
        });

        Schema::table('projects', function(Blueprint $table) {
            $table->boolean('uses_only_own_funds')->default(1);
            $table->string('main_recipient_id')->nullable()->comment('If a specific institution was selected, this is the id');
            $table->string('main_recipient')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
