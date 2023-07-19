<?php

namespace Database\Seeders;

use App\Models\UserFeedbackType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserFeedbackTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserFeedbackType::create(['name' => 'Bug']);
        UserFeedbackType::create(['name' => 'Feature Request']);
        UserFeedbackType::create(['name' => 'General Feedback']);
    }
}
