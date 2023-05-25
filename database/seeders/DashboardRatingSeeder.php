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
        DB::insert('INSERT INTO dashboard_rating (id, category, rating) VALUES (?, ?, ?)', [1, 'GREEN', 2.0]);
        DB::insert('INSERT INTO dashboard_rating (id, category, rating) VALUES (?, ?, ?)', [2, 'YELLOW', 1.5]);
        DB::insert('INSERT INTO dashboard_rating (id, category, rating) VALUES (?, ?, ?)', [3, 'YELLOW', 1.0]);
        DB::insert('INSERT INTO dashboard_rating (id, category, rating) VALUES (?, ?, ?)', [4, 'YELLOW', 0.5]);
        DB::insert('INSERT INTO dashboard_rating (id, category, rating) VALUES (?, ?, ?)', [5, 'RED', 0]);
    }
}
