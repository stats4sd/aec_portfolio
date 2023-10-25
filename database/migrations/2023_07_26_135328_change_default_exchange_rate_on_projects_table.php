<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // in case db has null values for exchange_rate:
        Project::withoutGlobalScopes(['organisation'])
            ->where('exchange_rate', '=', null)
            ->update(['exchange_rate' => 1.0]);


        Schema::table('projects', function (Blueprint $table) {
            $table->decimal('exchange_rate', 10, 6)->nullable(false)->default(1)->change();
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
