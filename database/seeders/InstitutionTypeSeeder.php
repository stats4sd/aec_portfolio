<?php

namespace Database\Seeders;

use App\Models\InstitutionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InstitutionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InstitutionType::create(['name' => 'NGO']);
        InstitutionType::create(['name' => 'Government']);
        InstitutionType::create(['name' => 'University']);
        InstitutionType::create(['name' => 'For-profit corporation']);
        InstitutionType::create(['name' => 'Other']);
    }
}
