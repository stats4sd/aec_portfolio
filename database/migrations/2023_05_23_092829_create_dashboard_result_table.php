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
        Schema::create('dashboard_result', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dashboard_id')->index();
            $table->unsignedBigInteger('dashboard_others_id')->index();
            $table->string('status');
            $table->string('message');
            $table->text('status_summary');
            $table->text('red_lines_summary');
            $table->text('principles_summary_yours');
            $table->text('principles_summary_others');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_result');
    }
};
