<?php

use App\Models\User;
use function Pest\Laravel\{actingAs};
use Database\Seeders\DatabaseSeeder;


beforeEach(function () {
    // Prepare something once before any of this file's tests run...
});


test('Site admin can access Continents, Regions, Countries, Users, Admin User Invites CRUD panels', function () {
    $siteAdmin = User::factory()->create();
    $siteAdmin->assignRole(['Site Admin']);

    actingAs($siteAdmin)->get('/admin/continent')->assertStatus(200);
    actingAs($siteAdmin)->get('/admin/region')->assertStatus(200);
    actingAs($siteAdmin)->get('/admin/country')->assertStatus(200);
    actingAs($siteAdmin)->get('/admin/user')->assertStatus(200);
    actingAs($siteAdmin)->get('/admin/role-invite')->assertStatus(200);
});


test('Site manager, institutional admin, institutional assessor, institutional member cannot access Continents, Regions, Countries, Users, Admin User Invites CRUD panels', function () {
    $siteManager = User::factory()->create();
    $siteManager->assignRole(['Site Manager']);

    $institutionalAdmin = User::factory()->create();
    $institutionalAdmin->assignRole(['Institutional Admin']);

    $institutionalAssessor = User::factory()->create();
    $institutionalAssessor->assignRole(['Institutional Assessor']);

    $institutionalMember = User::factory()->create();
    $institutionalMember->assignRole(['Institutional Member']);

    actingAs($siteManager)->get('/admin/continent')->assertStatus(403);
    actingAs($siteManager)->get('/admin/region')->assertStatus(403);
    actingAs($siteManager)->get('/admin/country')->assertStatus(403);
    actingAs($siteManager)->get('/admin/user')->assertStatus(403);
    actingAs($siteManager)->get('/admin/role-invite')->assertStatus(403);

    actingAs($institutionalAdmin)->get('/admin/continent')->assertStatus(403);
    actingAs($institutionalAdmin)->get('/admin/region')->assertStatus(403);
    actingAs($institutionalAdmin)->get('/admin/country')->assertStatus(403);
    actingAs($institutionalAdmin)->get('/admin/user')->assertStatus(403);
    actingAs($institutionalAdmin)->get('/admin/role-invite')->assertStatus(403);

    actingAs($institutionalAssessor)->get('/admin/continent')->assertStatus(403);
    actingAs($institutionalAssessor)->get('/admin/region')->assertStatus(403);
    actingAs($institutionalAssessor)->get('/admin/country')->assertStatus(403);
    actingAs($institutionalAssessor)->get('/admin/user')->assertStatus(403);
    actingAs($institutionalAssessor)->get('/admin/role-invite')->assertStatus(403);

    actingAs($institutionalMember)->get('/admin/continent')->assertStatus(403);
    actingAs($institutionalMember)->get('/admin/region')->assertStatus(403);
    actingAs($institutionalMember)->get('/admin/country')->assertStatus(403);
    actingAs($institutionalMember)->get('/admin/user')->assertStatus(403);
    actingAs($institutionalMember)->get('/admin/role-invite')->assertStatus(403);
});


test('Site admin, site manager can access Red Lines, Principles, Score Tags CRUD panels', function () {
    $siteAdmin = User::factory()->create();
    $siteAdmin->assignRole(['Site Admin']);

    $siteManager = User::factory()->create();
    $siteManager->assignRole(['Site Manager']);

    actingAs($siteAdmin)->get('/admin/red-line')->assertStatus(200);
    actingAs($siteAdmin)->get('/admin/principle')->assertStatus(200);
    actingAs($siteAdmin)->get('/admin/score-tag')->assertStatus(200);

    actingAs($siteManager)->get('/admin/red-line')->assertStatus(200);
    actingAs($siteManager)->get('/admin/principle')->assertStatus(200);
    actingAs($siteManager)->get('/admin/score-tag')->assertStatus(200);
});


test('Institutional admin, institutional assessor, institutional member cannot access Red Lines, Principles, Score Tags CRUD panels', function () {
    $institutionalAdmin = User::factory()->create();
    $institutionalAdmin->assignRole(['Institutional Admin']);

    $institutionalAssessor = User::factory()->create();
    $institutionalAssessor->assignRole(['Institutional Assessor']);

    $institutionalMember = User::factory()->create();
    $institutionalMember->assignRole(['Institutional Member']);

    actingAs($institutionalAdmin)->get('/admin/red-line')->assertStatus(403);
    actingAs($institutionalAdmin)->get('/admin/principle')->assertStatus(403);
    actingAs($institutionalAdmin)->get('/admin/score-tag')->assertStatus(403);

    actingAs($institutionalAssessor)->get('/admin/red-line')->assertStatus(403);
    actingAs($institutionalAssessor)->get('/admin/principle')->assertStatus(403);
    actingAs($institutionalAssessor)->get('/admin/score-tag')->assertStatus(403);

    actingAs($institutionalMember)->get('/admin/red-line')->assertStatus(403);
    actingAs($institutionalMember)->get('/admin/principle')->assertStatus(403);
    actingAs($institutionalMember)->get('/admin/score-tag')->assertStatus(403);
});



