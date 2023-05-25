<?php

namespace Tests\Feature\AdminPanel;

use Tests\TestCase;
use App\Models\User;
use App\Models\RedLine;

class RedLineCrudPageTest extends TestCase
{
    protected array $redLineData01;
    protected array $redLineData02;

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

        $this->redLineData01 = [
            'name' => 'fake red line name 01',
            'description' => 'fake red line description 01',
        ];

        $this->redLineData02 = [
            'name' => 'fake red line name 02',
            'description' => 'fake red line description 02',
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
            ->get('/admin/red-line')
            ->assertStatus(200);

        $this->actingAs($this->siteManager)
            ->get('/admin/red-line')
            ->assertStatus(200);


        // ********** //


        $this->actingAs($this->institutionalAdmin)
            ->get('/admin/red-line')
            ->assertStatus(403);

        $this->actingAs($this->institutionalAssessor)
            ->get('/admin/red-line')
            ->assertStatus(403);

        $this->actingAs($this->institutionalMember)
            ->get('/admin/red-line')
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
            ->post('admin/red-line/search')
            ->assertStatus(200);

        $this->actingAs($this->siteManager)
            ->post('admin/red-line/search')
            ->assertStatus(200);


        // ********** //


        $this->actingAs($this->institutionalAdmin)
            ->post('admin/red-line/search')
            ->assertStatus(403);

        $this->actingAs($this->institutionalAssessor)
            ->post('admin/red-line/search')
            ->assertStatus(403);

        $this->actingAs($this->institutionalMember)
            ->post('admin/red-line/search')
            ->assertStatus(403);
    }


    /**
     * SHOW OPERATION
     * @test
     */
    public function it_tries_to_load_the_show_view_with_different_users(): void
    {
        $redLine01 = RedLine::create($this->redLineData01);

        $this->actingAs($this->siteAdmin)
            ->get('/admin/red-line/' . $redLine01->id . '/show')
            ->assertStatus(200)
            ->assertSeeText($redLine01->name);

        $this->actingAs($this->siteManager)
            ->get('/admin/red-line/' . $redLine01->id . '/show')
            ->assertStatus(200)
            ->assertSeeText($redLine01->name);


        // ********** //


        $this->actingAs($this->institutionalAdmin)
            ->get('/admin/red-line/' . $redLine01->id . '/show')
            ->assertStatus(403);

        $this->actingAs($this->institutionalAssessor)
            ->get('/admin/red-line/' . $redLine01->id . '/show')
            ->assertStatus(403);

        $this->actingAs($this->institutionalMember)
            ->get('/admin/red-line/' . $redLine01->id . '/show')
            ->assertStatus(403);
    }


    /**
     * CREATE OPERATION
     * @test
     */
    public function it_tries_to_create_a_new_entry_with_different_users(): void
    {
        RedLine::truncate();


        // ********** //


        $this->actingAs($this->institutionalAdmin)
            ->get('/admin/red-line/create')
            ->assertStatus(403);


        // ********** //


        $this->actingAs($this->institutionalAssessor)
            ->get('/admin/red-line/create')
            ->assertStatus(403);


        // ********** //


        $this->actingAs($this->institutionalMember)
            ->get('/admin/red-line/create')
            ->assertStatus(403);


        // ********** //


        // Check the form loads
        $this->actingAs($this->siteAdmin)
            ->get('/admin/red-line/create')
            ->assertStatus(200)
            ->assertSee('Add red line');

        // Check the store endpoint works
        $this->actingAs($this->siteAdmin)
            ->post('/admin/red-line', $this->redLineData01);

        // check that the database contains the new red line
        $this->assertDatabaseHas(RedLine::class, $this->redLineData01);


        // ********** //


        // Check the form loads
        $this->actingAs($this->siteManager)
            ->get('/admin/red-line/create')
            ->assertStatus(200)
            ->assertSee('Add red line');

        // Check the store endpoint works
        $this->actingAs($this->siteManager)
            ->post('/admin/red-line', $this->redLineData02);

        // check that the database contains the new red line
        $this->assertDatabaseHas(RedLine::class, $this->redLineData02);
    }


    /**
     * UPDATE OPERATION
     * @test
     */
    public function it_tries_to_update_an_existing_entry_with_different_users(): void
    {
        $redLine01 = RedLine::where('name', $this->redLineData01['name'])->first();


        // ********** //


        // Check the edit form loads
        $this->actingAs($this->institutionalAdmin)
            ->get('/admin/red-line/' . $redLine01->id . '/edit')
            ->assertStatus(403);


        // ********** //


        // Check the edit form loads
        $this->actingAs($this->institutionalAssessor)
            ->get('/admin/red-line/' . $redLine01->id . '/edit')
            ->assertStatus(403);


        // ********** //


        // Check the edit form loads
        $this->actingAs($this->institutionalMember)
            ->get('/admin/red-line/' . $redLine01->id . '/edit')
            ->assertStatus(403);


        // ********** //


        $redLine01 = RedLine::where('name', $this->redLineData01['name'])->first();

        // Check the edit form loads
        $this->actingAs($this->siteAdmin)
            ->get('/admin/red-line/' . $redLine01->id . '/edit')
            ->assertStatus(200)
            ->assertSee('Edit red line');

        // Check the update endpoint works
        $response = $this->actingAs($this->siteAdmin)
            ->put('/admin/red-line/' . $redLine01->id,
                array_merge($this->redLineData01, [
                    'name' => 'edited fake red line name 01',
                    // update requests through Backpack include the id in the request body as well as the endpoint.
                    'id' => $redLine01->id,
                ])
            );

        $this->assertDatabaseHas(RedLine::class, ['id' => $redLine01->id, 'name' => 'edited fake red line name 01']);


        // ********** //


        $redLine02 = RedLine::where('name', $this->redLineData02['name'])->first();

        // Check the edit form loads
        $this->actingAs($this->siteManager)
            ->get('/admin/red-line/' . $redLine02->id . '/edit')
            ->assertStatus(200)
            ->assertSee('Edit red line');

        // Check the update endpoint works
        $response = $this->actingAs($this->siteManager)
            ->put('/admin/red-line/' . $redLine02->id,
                array_merge($this->redLineData02, [
                    'name' => 'edited fake red line name 02',
                    // update requests through Backpack include the id in the request body as well as the endpoint.
                    'id' => $redLine02->id,
                ])
            );

        $this->assertDatabaseHas(RedLine::class, ['id' => $redLine02->id, 'name' => 'edited fake red line name 02']);
    }


    /**
     * DELETE OPERATION
     * @test
     */
    public function it_tries_to_delete_an_existing_entry_with_different_users(): void
    {
        RedLine::truncate();


        // ********** //

        $redLine01 = RedLine::create($this->redLineData01);


        // ********** //


        $this->actingAs($this->institutionalAdmin)
            ->delete('/admin/red-line/' . $redLine01->id)
            ->assertStatus(403);


        // ********** //


        $this->actingAs($this->institutionalAssessor)
            ->delete('/admin/red-line/' . $redLine01->id)
            ->assertStatus(403);


        // ********** //


        $this->actingAs($this->institutionalMember)
            ->delete('/admin/red-line/' . $redLine01->id)
            ->assertStatus(403);


        // ********** //


        $this->assertDatabaseHas(RedLine::class, $this->redLineData01);

        $this->actingAs($this->siteAdmin)
            ->delete('/admin/red-line/' . $redLine01->id);

        $this->assertDatabaseMissing(RedLine::class, $this->redLineData01);


        // ********** //


        $redLine01 = RedLine::create($this->redLineData01);

        $this->assertDatabaseHas(RedLine::class, $this->redLineData01);

        $this->actingAs($this->siteManager)
            ->delete('/admin/red-line/' . $redLine01->id);

        $this->assertDatabaseMissing(RedLine::class, $this->redLineData01);
    }

}
