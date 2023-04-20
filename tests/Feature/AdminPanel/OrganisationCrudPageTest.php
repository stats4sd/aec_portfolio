<?php

namespace Tests\Feature\AdminPanel;

use Tests\TestCase;
use App\Models\User;
use App\Models\Principle;
use App\Models\Organisation;

class OrganisationCrudPageTest extends TestCase
{
    protected array $organisationData01;
    protected array $organisationData02;

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

        $this->organisationData01 = [
            'name' => 'fake institution name 01',
        ];

        $this->organisationData02 = [
            'name' => 'fake institution name 02',
        ];
    }

    /*********************** LIST OPERATION *********************/

    /**
     * LIST OPERATION
     * @test
     */
    public function it_tries_to_load_the_list_view_with_different_users(): void
    {
        $this->actingAs($this->siteAdmin)
            ->get('/admin/organisation')
            ->assertStatus(200);

        $this->actingAs($this->siteManager)
            ->get('/admin/organisation')
            ->assertStatus(200);


        // ********** //


        $this->actingAs($this->institutionalAdmin)
            ->get('/admin/organisation')
            ->assertStatus(403);

        $this->actingAs($this->institutionalAssessor)
            ->get('/admin/organisation')
            ->assertStatus(403);

        $this->actingAs($this->institutionalMember)
            ->get('/admin/organisation')
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
            ->post('admin/organisation/search')
            ->assertStatus(200);

        $this->actingAs($this->siteManager)
            ->post('admin/organisation/search')
            ->assertStatus(200);


        // ********** //


        $this->actingAs($this->institutionalAdmin)
            ->post('admin/organisation/search')
            ->assertStatus(403);

        $this->actingAs($this->institutionalAssessor)
            ->post('admin/organisation/search')
            ->assertStatus(403);

        $this->actingAs($this->institutionalMember)
            ->post('admin/organisation/search')
            ->assertStatus(403);
    }


    /**
     * SHOW OPERATION
     * @test
     */
    public function it_tries_to_load_the_show_view_with_different_users(): void
    {
        $organisation01 = Organisation::create($this->organisationData01);

        $this->actingAs($this->siteAdmin)
            ->get('/admin/organisation/' . $organisation01->id . '/show')
            ->assertStatus(200)
            ->assertSeeText($organisation01->name);

        $organisation01->delete();

        
        // TODO: show hide "Preview" button for site manager
        // Organisation CRUD panel and main menu items to be revised
        $organisation02 = Organisation::create($this->organisationData02);

        $this->actingAs($this->siteManager)
            ->get('/admin/organisation/' . $organisation02->id . '/show')
            ->assertStatus(200)
            ->assertSeeText($organisation02->name);

        $organisation02->delete();

        // ********** //


        $this->actingAs($this->institutionalAdmin)
            ->get('/admin/organisation/' . $organisation01->id . '/show')
            ->assertStatus(403);

        $this->actingAs($this->institutionalAssessor)
            ->get('/admin/organisation/' . $organisation01->id . '/show')
            ->assertStatus(403);

        $this->actingAs($this->institutionalMember)
            ->get('/admin/organisation/' . $organisation01->id . '/show')
            ->assertStatus(403);
    }


    /**
     * CREATE OPERATION
     * @test
     */
    public function it_tries_to_create_a_new_entry_with_different_users(): void
    {
        $this->actingAs($this->institutionalAdmin)
            ->get('/admin/organisation/create')
            ->assertStatus(403);


        // ********** //


        $this->actingAs($this->institutionalAssessor)
            ->get('/admin/organisation/create')
            ->assertStatus(403);


        // ********** //


        $this->actingAs($this->institutionalMember)
            ->get('/admin/organisation/create')
            ->assertStatus(403);


        // ********** //


        // Check the form loads
        $this->actingAs($this->siteAdmin)
            ->get('/admin/organisation/create')
            ->assertStatus(200)
            ->assertSee('Add institution');

        // Check the store endpoint works
        $this->actingAs($this->siteAdmin)
            ->post('/admin/organisation', $this->organisationData01);

        // check that the database contains the new red line
        $this->assertDatabaseHas(Organisation::class, $this->organisationData01);


        // ********** //


        // Check the form loads
        $this->actingAs($this->siteManager)
            ->get('/admin/organisation/create')
            ->assertStatus(200)
            ->assertSee('Add institution');

        // Check the store endpoint works
        $this->actingAs($this->siteAdmin)
            ->post('/admin/organisation', $this->organisationData02);

        // check that the database contains the new red line
        $this->assertDatabaseHas(Organisation::class, $this->organisationData02);
    }



    /**
     * UPDATE OPERATION
     * @test
     */
    public function it_tries_to_update_an_existing_entry_with_different_users(): void
    {
        $organisation01 = Organisation::where('name', $this->organisationData01['name'])->first();


        // ********** //


        // Check the edit form loads
        $this->actingAs($this->institutionalAdmin)
            ->get('/admin/organisation/' . $organisation01->id . '/edit')
            ->assertStatus(403);


        // ********** //


        // Check the edit form loads
        $this->actingAs($this->institutionalAssessor)
            ->get('/admin/organisation/' . $organisation01->id . '/edit')
            ->assertStatus(403);


        // ********** //


        // Check the edit form loads
        $this->actingAs($this->institutionalMember)
            ->get('/admin/organisation/' . $organisation01->id . '/edit')
            ->assertStatus(403);


        // ********** //


        // Check the edit form loads
        $this->actingAs($this->siteAdmin)
            ->get('/admin/organisation/' . $organisation01->id . '/edit')
            ->assertStatus(200)
            ->assertSee('Edit institution');

        // Check the update endpoint works
        $response = $this->actingAs($this->siteAdmin)
            ->put('/admin/organisation/' . $organisation01->id,
                array_merge($this->organisationData01, [
                    'name' => 'edited fake institution name 01',
                    // update requests through Backpack include the id in the request body as well as the endpoint.
                    'id' => $organisation01->id,
                ])
            );

        $this->assertDatabaseHas(Organisation::class, ['id' => $organisation01->id, 'name' => 'edited fake institution name 01']);

        $organisation01->delete();


        // ********** //


        $organisation02 = Organisation::where('name', $this->organisationData02['name'])->first();

        // Check the edit form loads
        $this->actingAs($this->siteManager)
            ->get('/admin/organisation/' . $organisation02->id . '/edit')
            ->assertStatus(200)
            ->assertSee('Edit institution');

        // Check the update endpoint works
        $response = $this->actingAs($this->siteManager)
            ->put('/admin/organisation/' . $organisation02->id,
                array_merge($this->organisationData02, [
                    'name' => 'edited fake institution name 02',
                    // update requests through Backpack include the id in the request body as well as the endpoint.
                    'id' => $organisation02->id,
                ])
            );

        $this->assertDatabaseHas(Organisation::class, ['id' => $organisation02->id, 'name' => 'edited fake institution name 02']);

        $organisation02->delete();

    }


    /**
     * DELETE OPERATION
     * @test
     */
    public function it_tries_to_delete_an_existing_red_line_with_different_users(): void
    {
        $organisation01 = Organisation::create($this->organisationData01);


        // ********** //


        $this->actingAs($this->institutionalAdmin)
            ->delete('/admin/organisation/' . $organisation01->id)
            ->assertStatus(403);


        // ********** //


        $this->actingAs($this->institutionalAssessor)
            ->delete('/admin/organisation/' . $organisation01->id)
            ->assertStatus(403);


        // ********** //


        $this->actingAs($this->institutionalMember)
            ->delete('/admin/organisation/' . $organisation01->id)
            ->assertStatus(403);


        // ********** //


        $this->actingAs($this->siteAdmin)
            ->delete('/admin/organisation/' . $organisation01->id)
            ->assertStatus(403);


        // ********** //


        $this->actingAs($this->siteManager)
            ->delete('/admin/organisation/' . $organisation01->id)
            ->assertStatus(403);

    }

}
