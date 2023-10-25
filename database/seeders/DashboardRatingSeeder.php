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
        // mix_rating and max_rating will be used for principle rating in dashbaord
        // the condition in SQL is something like:
        //      WHERE ta.rating >= tb.min_rating
        //      AND ta.rating < tb.max_rating
        // 
        // The actual range for different rating are:
        // RED:    0.00 - 0.49
        // YELLOW: 0.50 - 1.49
        // GREEN:  1.50 - 2.00

        // due to the WHERE clause in above SQL, max_rating for GREEN needs to be 2.01 instead of 2.00
        // P.S. If max_rating is 2.00, those principle with score 2.00 will be excluded
        DB::insert('INSERT INTO dashboard_rating (id, category, min_rating, max_rating) VALUES (?, ?, ?, ?)', [1, 'GREEN', 1.5, 2.01]);

        DB::insert('INSERT INTO dashboard_rating (id, category, min_rating, max_rating) VALUES (?, ?, ?, ?)', [2, 'YELLOW', 0.5, 1.5]);

        DB::insert('INSERT INTO dashboard_rating (id, category, min_rating, max_rating) VALUES (?, ?, ?, ?)', [3, 'RED', 0, 0.5]);
    }
}
