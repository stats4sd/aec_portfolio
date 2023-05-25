<?php

namespace Database\Seeders;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::factory()->create(['name' => 'Site Admin', 'email' => 'site_admin@example.com']);
        $user->assignRole('Site Admin');

        $user = User::factory()->create(['name' => 'Site Manager', 'email' => 'site_manager@example.com']);
        $user->assignRole('Site Manager');

        // TODO: assign institutional users to an organisation
        $user = User::factory()->create(['name' => 'Institutional Admin', 'email' => 'ins_admin@example.com']);
        $user->assignRole('Institutional Admin');

        $user = User::factory()->create(['name' => 'Institutional Assessor', 'email' => 'ins_assessor@example.com']);
        $user->assignRole('Institutional Assessor');

        $user = User::factory()->create(['name' => 'Institutional Member', 'email' => 'ins_member@example.com']);
        $user->assignRole('Institutional Member');

        $institution = Organisation::create([
            'name' => 'Test Institution 1',
        ]);

        $portfolio = $institution->portfolios()->create([
            'name' => 'Test Portfolio 1',
        ]);

        $initiative = $portfolio->projects()->create([
            'name' => 'Test Project 1',
            'organisation_id' => $institution->id,
            'code' => 'TP1',
            'description' => 'A project for testing',
            'budget' => '1000000',
            'currency' => 'EUR',
            'start_date' => '2023-01-01',
            'geographic_reach' => 'global',
        ]);
    }
}
