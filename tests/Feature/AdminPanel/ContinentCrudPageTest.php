<?php

namespace Tests\Feature\AdminPanel;

use Tests\TestCase;
use App\Models\User;
use App\Models\Continent;

class ContinentCrudPageTest extends TestCase
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
            ->get('/admin/continent')
            ->assertStatus(200);

        // ********** //

        $this->actingAs($this->siteManager)
            ->get('/admin/continent')
            ->assertStatus(403);

        $this->actingAs($this->institutionalAdmin)
            ->get('/admin/continent')
            ->assertStatus(403);

        $this->actingAs($this->institutionalAssessor)
            ->get('/admin/continent')
            ->assertStatus(403);

        $this->actingAs($this->institutionalMember)
            ->get('/admin/continent')
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
            ->post('admin/continent/search')
            ->assertStatus(200);

        // ********** //

        $this->actingAs($this->siteManager)
            ->post('admin/continent/search')
            ->assertStatus(403);

        $this->actingAs($this->institutionalAdmin)
            ->post('admin/continent/search')
            ->assertStatus(403);

        $this->actingAs($this->institutionalAssessor)
            ->post('admin/continent/search')
            ->assertStatus(403);

        $this->actingAs($this->institutionalMember)
            ->post('admin/continent/search')
            ->assertStatus(403);
    }


    /**
     * SHOW OPERATION
     * @test
     */
    public function it_tries_to_load_the_show_view_with_different_users(): void
    {
        $continent01 = Continent::first();

        $this->actingAs($this->siteAdmin)
            ->get('/admin/continent/' . $continent01->id . '/show')
            ->assertStatus(404);

        $this->actingAs($this->siteManager)
            ->get('/admin/continent/' . $continent01->id . '/show')
            ->assertStatus(404);

        $this->actingAs($this->institutionalAdmin)
            ->get('/admin/continent/' . $continent01->id . '/show')
            ->assertStatus(404);

        $this->actingAs($this->institutionalAssessor)
            ->get('/admin/continent/' . $continent01->id . '/show')
            ->assertStatus(404);

        $this->actingAs($this->institutionalMember)
            ->get('/admin/continent/' . $continent01->id . '/show')
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
            ->get('/admin/continent/create')
            ->assertStatus(404);

        $this->actingAs($this->siteManager)
            ->get('/admin/continent/create')
            ->assertStatus(404);

        $this->actingAs($this->institutionalAdmin)
            ->get('/admin/continent/create')
            ->assertStatus(404);

        $this->actingAs($this->institutionalAssessor)
            ->get('/admin/continent/create')
            ->assertStatus(404);

        $this->actingAs($this->institutionalMember)
            ->get('/admin/continent/create')
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
            ->get('/admin/continent/edit')
            ->assertStatus(404);

        $this->actingAs($this->siteManager)
            ->get('/admin/continent/edit')
            ->assertStatus(404);

        $this->actingAs($this->institutionalAdmin)
            ->get('/admin/continent/edit')
            ->assertStatus(404);

        $this->actingAs($this->institutionalAssessor)
            ->get('/admin/continent/edit')
            ->assertStatus(404);

        $this->actingAs($this->institutionalMember)
            ->get('/admin/continent/edit')
            ->assertStatus(404);
    }


    /**
     * DELETE OPERATION
     * @test
     */
    public function it_cannot_delete_an_existing_entry_with_different_users(): void
    {
        $continent = Continent::first();

        // Check the form loads
        $this->actingAs($this->siteAdmin)
            ->delete('/admin/continent' . $continent->id)
            ->assertStatus(404);

        $this->actingAs($this->siteManager)
            ->delete('/admin/continent' . $continent->id)
            ->assertStatus(404);

        $this->actingAs($this->institutionalAdmin)
            ->delete('/admin/continent' . $continent->id)
            ->assertStatus(404);

        $this->actingAs($this->institutionalAssessor)
            ->delete('/admin/continent' . $continent->id)
            ->assertStatus(404);

        $this->actingAs($this->institutionalMember)
            ->delete('/admin/continent' . $continent->id)
            ->assertStatus(404);
    }

}
