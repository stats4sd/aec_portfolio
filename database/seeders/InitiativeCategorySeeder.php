<?php

namespace Database\Seeders;

use App\Models\InitiativeCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InitiativeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InitiativeCategory::create(['name' => 'Field projects']);
        InitiativeCategory::create(['name' => 'Policy + Advocacy project']);
        InitiativeCategory::create(['name' => 'Research Project']);
        InitiativeCategory::create(['name' => 'Entrepreneurship / Private sector']);
        InitiativeCategory::create(['name' => 'Other']);
    }
}
