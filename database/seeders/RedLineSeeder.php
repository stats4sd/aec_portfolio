<?php

namespace Database\Seeders;

use App\Models\RedLine;
use Illuminate\Database\Seeder;

class RedLineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RedLine::factory()->count(5)->create();
    }
}
