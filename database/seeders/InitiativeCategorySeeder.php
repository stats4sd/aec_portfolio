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
        InitiativeCategory::create(['name' => 'Policy and/or advocacy project']);
        InitiativeCategory::create(['name' => 'Research project']);
        InitiativeCategory::create(['name' => 'Entrepreneurship / private sector']);
        InitiativeCategory::create(['name' => 'Other']);
    }
}
