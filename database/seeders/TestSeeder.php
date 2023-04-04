<?php

namespace Database\Seeders;

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
    }
}
