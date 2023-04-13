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




