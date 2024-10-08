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
            $table->string('status')->nullable();
            $table->string('message')->nullable();
            $table->unsignedBigInteger('total_count')->nullable();
            $table->unsignedBigInteger('total_budget')->nullable();
            $table->boolean('too_few_others')->default(0)
                ->comment('Is true if there are too few "other" initiatives or institutions to do an anonymous comparison');
            $table->text('status_summary')->nullable();
            $table->text('red_lines_summary')->nullable();
            $table->text('principles_summary_yours')->nullable();
            $table->text('principles_summary_others')->nullable();
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
