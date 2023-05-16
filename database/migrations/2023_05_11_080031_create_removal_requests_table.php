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
        Schema::create('removal_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organisation_id');
            $table->unsignedBigInteger('requester_id');
            $table->string('status');
            $table->timestamp('requested_at');
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('reminded_at')->nullable();
            $table->timestamp('final_confirmed_at')->nullable();
            $table->timestamp('everything_removed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('removal_requests');
    }
};
