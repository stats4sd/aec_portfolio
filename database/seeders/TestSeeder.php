<?php

namespace Database\Seeders;

use App\Enums\GeographicalReach;
use App\Models\Continent;
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

        $institution = Organisation::create([
            'name' => 'Test Institution 1',
            'geographic_reach' => GeographicalReach::Global->value,
            'currency' => 'EUR',
            'has_additional_criteria' => 0,
            'description' => 'This is a test organisation',
        ]);


        $admin = User::factory()->create(['name' => 'Site Admin', 'email' => 'site_admin@example.com']);
        $manager = User::factory()->create(['name' => 'Site Manager', 'email' => 'site_manager@example.com']);


        setPermissionsTeamId($institution->id);

        $admin->assignRole('Site Admin');
        $manager->assignRole('Site Manager');


        // Add some test users to the test organisation

        $user1 = User::factory()->create(['name' => 'Institutional Admin', 'email' => 'ins_admin@example.com']);
        $user1->assignRole('Institutional Admin');

        $user2 = User::factory()->create(['name' => 'Institutional Assessor', 'email' => 'ins_assessor@example.com']);
        $user2->assignRole('Institutional Assessor');

        $user3 = User::factory()->create(['name' => 'Institutional Member', 'email' => 'ins_member@example.com']);
        $user3->assignRole('Institutional Member');

        $portfolio = $institution->portfolios()->create([
            'name' => 'Test Portfolio 1',
        ]);

        $initiative = $portfolio->projects()->create([
            'name' => 'Test Project 1',
            'organisation_id' => $institution->id,
            'code' => 'TP1',
            'description' => 'A project for testing',
            'budget' => '1000000',
            'budget_eur' => '1000000',
            'budget_org' => '1000000',
            'exchange_rate_eur' => 1,
            'exchange_rate' => 1,
            'currency' => 'EUR',
            'start_date' => '2023-01-01',
            'geographic_reach' => GeographicalReach::Global->name,
            'main_recipient' => 'Test Recipient',
            'initiative_category_id' => 1,
            'sub_regions' => 'Test Sub Region',
        ]);

        $continents = Continent::take(2);
        $regions = $continents->first()->regions()->take(2);
        $countries = $regions->first()->countries()->take(4);

        $initiative->continents()->sync($continents->pluck('id'));
        $initiative->regions()->sync($regions->pluck('id'));
        $initiative->countries()->sync($countries->pluck('id'));

        $donorInstitution = Organisation::create([
            'name' => 'Test Donor 1',
            'geographic_reach' => GeographicalReach::Global->name,
            'currency' => 'EUR',
            'has_additional_criteria' => 0,
            'description' => 'This is a test donor organisation',
        ]);

        $initiative->fundingSources()->createMany([
            ['source' => 'Funding Source 1', 'amount' => 500000],
            ['institution_id' => $donorInstitution->id, 'amount' => 500000],
        ]);

        $user1->organisations()->sync([$institution->id]);
        $user2->organisations()->sync([$institution->id]);
        $user3->organisations()->sync([$institution->id]);
    }
}
