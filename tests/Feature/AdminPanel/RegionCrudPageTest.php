<?php

namespace Tests\Feature\AdminPanel;

use Tests\TestCase;
use App\Models\User;
use App\Models\Region;

class RegionCrudPageTest extends TestCase
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
            ->get('/admin/region')
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
            ->post('admin/region/search')
            ->assertStatus(200);
    }

    /**
     * CREATE OPERATION
     * @test
     */
    public function it_cannot_create_a_new_region(): void
    {
        // Check the form loads
        $this->actingAs($this->user)
            ->get('/admin/region/create')
            ->assertStatus(404);
    }

    /**
     * UPDATE OPERATION
     * @test
     */
    public function it_cannot_update_an_existing_region(): void
    {
        // Check the form loads
        $this->actingAs($this->user)
            ->get('/admin/region/edit')
            ->assertStatus(404);
    }

    /**
     * DELETE OPERATION
     * @test
     */
    public function it_cannot_delete_an_existing_region(): void
    {
        $region = Region::first();

        // Check the form loads
        $this->actingAs($this->user)
            ->delete('/admin/region' . $region->id)
            ->assertStatus(404);
    }

}
