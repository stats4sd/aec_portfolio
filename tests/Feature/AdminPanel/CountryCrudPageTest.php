<?php

namespace Tests\Feature\AdminPanel;

use Tests\TestCase;
use App\Models\User;
use App\Models\Country;

class CountryCrudPageTest extends TestCase
{
    protected User $siteAdmin;
    protected User $siteManager;
    protected User $institutionalAdmin;
    protected User $institutionalAssessor;
    protected User $institutionalMember;

    public function setUp(): void
    {
        parent::setUp();

        $this->siteAdmin = $this->setupSiteAdminUser();
        $this->siteManager = $this->setupSiteManagerUser();
        $this->institutionalAdmin = $this->setupInstitutionalAdminUser();
        $this->institutionalAssessor = $this->setupInstitutionalAssessorUser();
        $this->institutionalMember = $this->setupInstitutionalMemberUser();

        $this->seedLocations();
    }

    /*********************** LIST OPERATION *********************/

    /**
     * LIST OPERATION
     * @test
     */
    public function it_tries_to_load_the_list_view_with_different_users(): void
    {
        $this->actingAs($this->siteAdmin)
            ->get('/admin/country')
            ->assertStatus(200);

        // ********** //

        $this->actingAs($this->siteManager)
            ->get('/admin/country')
            ->assertStatus(403);

        $this->actingAs($this->institutionalAdmin)
            ->get('/admin/country')
            ->assertStatus(403);

        $this->actingAs($this->institutionalAssessor)
            ->get('/admin/country')
            ->assertStatus(403);

        $this->actingAs($this->institutionalMember)
            ->get('/admin/country')
            ->assertStatus(403);
    }


    /**
     * SEARCH OPERATION
     * @test
     */
    public function it_tries_to_search_the_list_view_with_different_users(): void
    {
        # check the search endpoint that returns the table contents
        $this->actingAs($this->siteAdmin)
            ->post('admin/country/search')
            ->assertStatus(200);

        // ********** //

        $this->actingAs($this->siteManager)
            ->post('admin/country/search')
            ->assertStatus(403);

        $this->actingAs($this->institutionalAdmin)
            ->post('admin/country/search')
            ->assertStatus(403);

        $this->actingAs($this->institutionalAssessor)
            ->post('admin/country/search')
            ->assertStatus(403);

        $this->actingAs($this->institutionalMember)
            ->post('admin/country/search')
            ->assertStatus(403);
    }


    /**
     * SHOW OPERATION
     * @test
     */
    public function it_tries_to_load_the_show_view_with_different_users(): void
    {
        $country01 = Country::first();

        $this->actingAs($this->siteAdmin)
            ->get('/admin/country/' . $country01->id . '/show')
            ->assertStatus(404);

        $this->actingAs($this->siteManager)
            ->get('/admin/country/' . $country01->id . '/show')
            ->assertStatus(404);

        $this->actingAs($this->institutionalAdmin)
            ->get('/admin/country/' . $country01->id . '/show')
            ->assertStatus(404);

        $this->actingAs($this->institutionalAssessor)
            ->get('/admin/country/' . $country01->id . '/show')
            ->assertStatus(404);

        $this->actingAs($this->institutionalMember)
            ->get('/admin/country/' . $country01->id . '/show')
            ->assertStatus(404);
    }


    /**
     * CREATE OPERATION
     * @test
     */
    public function it_cannot_create_a_new_entry_with_different_users(): void
    {
        // Check the form loads
        $this->actingAs($this->siteAdmin)
            ->get('/admin/country/create')
            ->assertStatus(404);

        $this->actingAs($this->siteManager)
            ->get('/admin/country/create')
            ->assertStatus(404);

        $this->actingAs($this->institutionalAdmin)
            ->get('/admin/country/create')
            ->assertStatus(404);

        $this->actingAs($this->institutionalAssessor)
            ->get('/admin/country/create')
            ->assertStatus(404);

        $this->actingAs($this->institutionalMember)
            ->get('/admin/country/create')
            ->assertStatus(404);    
    }


    /**
     * UPDATE OPERATION
     * @test
     */
    public function it_cannot_update_an_existing_entry_with_different_users(): void
    {
        // Check the form loads
        $this->actingAs($this->siteAdmin)
            ->get('/admin/country/edit')
            ->assertStatus(404);

        $this->actingAs($this->siteManager)
            ->get('/admin/country/edit')
            ->assertStatus(404);

        $this->actingAs($this->institutionalAdmin)
            ->get('/admin/country/edit')
            ->assertStatus(404);

        $this->actingAs($this->institutionalAssessor)
            ->get('/admin/country/edit')
            ->assertStatus(404);

        $this->actingAs($this->institutionalMember)
            ->get('/admin/country/edit')
            ->assertStatus(404);
    }


    /**
     * DELETE OPERATION
     * @test
     */
    public function it_cannot_delete_an_existing_entry_with_different_users(): void
    {
        $country = Country::first();

        // Check the form loads
        $this->actingAs($this->siteAdmin)
            ->delete('/admin/country' . $country->id)
            ->assertStatus(404);

        $this->actingAs($this->siteManager)
            ->delete('/admin/country' . $country->id)
            ->assertStatus(404);

        $this->actingAs($this->institutionalAdmin)
            ->delete('/admin/country' . $country->id)
            ->assertStatus(404);

        $this->actingAs($this->institutionalAssessor)
            ->delete('/admin/country' . $country->id)
            ->assertStatus(404);

        $this->actingAs($this->institutionalMember)
            ->delete('/admin/country' . $country->id)
            ->assertStatus(404);
    }

}
