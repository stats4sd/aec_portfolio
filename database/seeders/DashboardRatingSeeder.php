<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DashboardRatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::insert('INSERT INTO dashboard_rating (id, category, min_rating, max_rating) VALUES (?, ?, ?, ?)', [1, 'GREEN', 1.5, 2.0]);
        DB::insert('INSERT INTO dashboard_rating (id, category, min_rating, max_rating) VALUES (?, ?, ?, ?)', [2, 'YELLOW', 0.5, 1.5]);
        DB::insert('INSERT INTO dashboard_rating (id, category, min_rating, max_rating) VALUES (?, ?, ?, ?)', [3, 'RED', 0, 0.5]);
    }
}
