<?php

namespace Tests\Feature\AdminPanel;

use Tests\TestCase;
use App\Models\User;
use App\Models\Country;

class CountryCrudPageTest extends TestCase
{
    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->setupSiteAdminUser();

        $this->seedLocations();
    }

    /*********************** LIST OPERATION *********************/

    /**
     * LIST OPERATION
     * @test
     */
    public function it_loads_the_list_view(): void
    {
        $this->actingAs($this->user)
            ->get('/admin/country')
            ->assertStatus(200);
    }

    /**
     * SEARCH OPERATION
     * @test
     */
    public function it_searches_the_list_view(): void
    {
        # check the search endpoint that returns the table contents
        $this->actingAs($this->user)
            ->post('admin/country/search')
            ->assertStatus(200);
    }

    /**
     * CREATE OPERATION
     * @test
     */
    public function it_cannot_create_a_new_country(): void
    {
        // Check the form loads
        $this->actingAs($this->user)
            ->get('/admin/country/create')
            ->assertStatus(404);
    }

    /**
     * UPDATE OPERATION
     * @test
     */
    public function it_cannot_update_an_existing_country(): void
    {
        // Check the form loads
        $this->actingAs($this->user)
            ->get('/admin/country/edit')
            ->assertStatus(404);
    }

    /**
     * DELETE OPERATION
     * @test
     */
    public function it_cannot_delete_an_existing_country(): void
    {
        $country = Country::first();

        // Check the form loads
        $this->actingAs($this->user)
            ->delete('/admin/country' . $country->id)
            ->assertStatus(404);
    }

}
