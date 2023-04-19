<?php

namespace Tests\Feature\AdminPanel;

use Tests\TestCase;
use App\Models\User;
use App\Models\ScoreTag;

class ScoreTagCrudPageTest extends TestCase
{
    protected array $scoreTagData01;
    protected array $scoreTagData02;

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

        $this->scoreTagData01 = [
            'principle_id' => 1,
            'name' => 'fake score tag name 01',
            'description' => 'fake score tag description 01',
        ];

        $this->scoreTagData02 = [
            'principle_id' => 2,
            'name' => 'fake score tag name 02',
            'description' => 'fake score tag description 02',
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
            ->get('/admin/score-tag')
            ->assertStatus(200);

        $this->actingAs($this->siteManager)
            ->get('/admin/score-tag')
            ->assertStatus(200);


        // ********** //


        $this->actingAs($this->institutionalAdmin)
            ->get('/admin/score-tag')
            ->assertStatus(403);

        $this->actingAs($this->institutionalAssessor)
            ->get('/admin/score-tag')
            ->assertStatus(403);

        $this->actingAs($this->institutionalMember)
            ->get('/admin/score-tag')
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
            ->post('/admin/score-tag/search')
            ->assertStatus(200);

        $this->actingAs($this->siteManager)
            ->post('/admin/score-tag/search')
            ->assertStatus(200);


        // ********** //


        $this->actingAs($this->institutionalAdmin)
            ->post('/admin/score-tag/search')
            ->assertStatus(403);

        $this->actingAs($this->institutionalAssessor)
            ->post('/admin/score-tag/search')
            ->assertStatus(403);

        $this->actingAs($this->institutionalMember)
            ->post('/admin/score-tag/search')
            ->assertStatus(403);
    }


    /**
     * SHOW OPERATION
     * @test
     */
    public function it_tries_to_load_the_show_view_with_different_users(): void
    {
        $scoreTag01 = ScoreTag::create($this->scoreTagData01);

        $this->actingAs($this->siteAdmin)
            ->get('/admin/score-tag/' . $scoreTag01->id . '/show')
            ->assertStatus(200)
            ->assertSeeText($scoreTag01->name);

        $this->actingAs($this->siteManager)
            ->get('/admin/score-tag/' . $scoreTag01->id . '/show')
            ->assertStatus(200)
            ->assertSeeText($scoreTag01->name);


        // ********** //


        $this->actingAs($this->institutionalAdmin)
            ->get('/admin/score-tag/' . $scoreTag01->id . '/show')
            ->assertStatus(403);

        $this->actingAs($this->institutionalAssessor)
            ->get('/admin/score-tag/' . $scoreTag01->id . '/show')
            ->assertStatus(403);

        $this->actingAs($this->institutionalMember)
            ->get('/admin/score-tag/' . $scoreTag01->id . '/show')
            ->assertStatus(403);
    }


    /**
     * CREATE OPERATION
     * @test
     */
    public function it_tries_to_create_a_new_entry_with_different_users(): void
    {
        ScoreTag::truncate();
        

        // ********** //


        $this->actingAs($this->institutionalAdmin)
            ->get('/admin/score-tag/create')
            ->assertStatus(403);


        // ********** //


        $this->actingAs($this->institutionalAssessor)
            ->get('/admin/score-tag/create')
            ->assertStatus(403);


        // ********** //


        $this->actingAs($this->institutionalMember)
            ->get('/admin/score-tag/create')
            ->assertStatus(403);


        // ********** //


        // Check the form loads
        $this->actingAs($this->siteAdmin)
            ->get('/admin/score-tag/create')
            ->assertStatus(200)
            ->assertSee('Add score tag');


        ScoreTag::create($this->scoreTagData01);

        // Check the store endpoint works
        // $this->actingAs($this->siteAdmin)
        //     ->post('/admin/score-tag', $this->scoreTagData01);

        // check that the database contains the new red line
        $this->assertDatabaseHas(ScoreTag::class, $this->scoreTagData01);


        // ********** //


        // Check the form loads
        $this->actingAs($this->siteManager)
            ->get('/admin/score-tag/create')
            ->assertStatus(200)
            ->assertSee('Add score tag');

        ScoreTag::create($this->scoreTagData02);

        // Check the store endpoint works
        // $this->actingAs($this->siteManager)
        //     ->post('/admin/score-tag', $this->scoreTagData02);

        // check that the database contains the new red line
        $this->assertDatabaseHas(ScoreTag::class, $this->scoreTagData02);
    }


    /**
     * UPDATE OPERATION
     * @test
     */
    public function it_tries_to_update_an_existing_entry_with_different_users(): void
    {
        $scoreTag01 = ScoreTag::where('name', $this->scoreTagData01['name'])->first();


        // ********** //


        // Check the edit form loads
        $this->actingAs($this->institutionalAdmin)
            ->get('/admin/score-tag/' . $scoreTag01->id . '/edit')
            ->assertStatus(403);


        // ********** //


        // Check the edit form loads
        $this->actingAs($this->institutionalAssessor)
            ->get('/admin/score-tag/' . $scoreTag01->id . '/edit')
            ->assertStatus(403);


        // ********** //


        // Check the edit form loads
        $this->actingAs($this->institutionalMember)
            ->get('/admin/score-tag/' . $scoreTag01->id . '/edit')
            ->assertStatus(403);


        // ********** //


        $scoreTag01 = ScoreTag::where('name', $this->scoreTagData01['name'])->first();

        // Check the edit form loads
        $this->actingAs($this->siteAdmin)
            ->get('/admin/score-tag/' . $scoreTag01->id . '/edit')
            ->assertStatus(200)
            ->assertSee('Edit score tag');

        // Check the update endpoint works
        $response = $this->actingAs($this->siteAdmin)
            ->put('/admin/score-tag/' . $scoreTag01->id,
                array_merge($this->scoreTagData01, [
                    'name' => 'edited fake score tag name 01',
                    // update requests through Backpack include the id in the request body as well as the endpoint.
                    'id' => $scoreTag01->id,
                ])
            );

        $this->assertDatabaseHas(ScoreTag::class, ['id' => $scoreTag01->id, 'name' => 'edited fake score tag name 01']);


        // ********** //


        $scoreTag02 = ScoreTag::where('name', $this->scoreTagData02['name'])->first();

        // Check the edit form loads
        $this->actingAs($this->siteManager)
            ->get('/admin/score-tag/' . $scoreTag02->id . '/edit')
            ->assertStatus(200)
            ->assertSee('Edit score tag');

        // Check the update endpoint works
        $response = $this->actingAs($this->siteManager)
            ->put('/admin/score-tag/' . $scoreTag02->id,
                array_merge($this->scoreTagData02, [
                    'name' => 'edited fake score tag name 02',
                    // update requests through Backpack include the id in the request body as well as the endpoint.
                    'id' => $scoreTag02->id,
                ])
            );

        $this->assertDatabaseHas(ScoreTag::class, ['id' => $scoreTag02->id, 'name' => 'edited fake score tag name 02']);
    }


    /**
     * DELETE OPERATION
     * @test
     */
    public function it_tries_to_delete_an_existing_red_line_with_different_users(): void
    {
        ScoreTag::truncate();


        // ********** //

        $scoreTag01 = ScoreTag::create($this->scoreTagData01);


        // ********** //


        $this->actingAs($this->institutionalAdmin)
            ->delete('/admin/score-tag/' . $scoreTag01->id)
            ->assertStatus(403);


        // ********** //


        $this->actingAs($this->institutionalAssessor)
            ->delete('/admin/score-tag/' . $scoreTag01->id)
            ->assertStatus(403);


        // ********** //


        $this->actingAs($this->institutionalMember)
            ->delete('/admin/score-tag/' . $scoreTag01->id)
            ->assertStatus(403);


        // ********** //


        $this->assertDatabaseHas(ScoreTag::class, $this->scoreTagData01);

        $this->actingAs($this->siteAdmin)
            ->delete('/admin/score-tag/' . $scoreTag01->id);

        $this->assertDatabaseMissing(ScoreTag::class, $this->scoreTagData01);


        // ********** //


        $scoreTag01 = ScoreTag::create($this->scoreTagData01);

        $this->assertDatabaseHas(ScoreTag::class, $this->scoreTagData01);

        $this->actingAs($this->siteManager)
            ->delete('/admin/score-tag/' . $scoreTag01->id);

        $this->assertDatabaseMissing(ScoreTag::class, $this->scoreTagData01);
    }

}
