<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ContinentSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(RedLinesTableSeeder::class);
        $this->call(PrinciplesTableSeeder::class);
        $this->call(TestSeeder::class);
        $this->call(DashboardRatingSeeder::class);
        $this->call(ProjectSeeder::class);
        $this->call(ScoreTagsTableSeeder::class);
        $this->call(ExchangeRatesTableSeeder::class);
    }
}
