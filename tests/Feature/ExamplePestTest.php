<?php

use App\Models\User;
use function Pest\Laravel\{actingAs};
use Database\Seeders\DatabaseSeeder;


beforeEach(function () {
    // Prepare something once before any of this file's tests run...
});


/*
Testing summary:
1. Check whether different roles can access different CRUD panels properly
2. Check whether different menu items appear for different roles properly
*/

test('1. Check whether different roles can access different CRUD panels properly')->todo();

test('--- 1.1 Site admin CAN access Continents, Regions, Countries, Users, Admin User Invites CRUD panels', function () {
    $siteAdmin = User::factory()->create();
    $siteAdmin->assignRole(['Site Admin']);

    actingAs($siteAdmin)->get('/admin/continent')->assertStatus(200);
    actingAs($siteAdmin)->get('/admin/region')->assertStatus(200);
    actingAs($siteAdmin)->get('/admin/country')->assertStatus(200);
    actingAs($siteAdmin)->get('/admin/user')->assertStatus(200);
    actingAs($siteAdmin)->get('/admin/role-invite')->assertStatus(200);
});


test('--- 1.2 Site manager, institutional admin, institutional assessor, institutional member CANNOT access Continents, Regions, Countries, Users, Admin User Invites CRUD panels', function () {
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


test('--- 1.3 Site admin, site manager CAN access Red Lines, Principles, Score Tags CRUD panels', function () {
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


test('--- 1.4 Institutional admin, institutional assessor, institutional member CANNOT access Red Lines, Principles, Score Tags CRUD panels', function () {
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


test('--- 1.5 Site admin, site manager CAN access Institutions CRUD panel', function () {
    $siteAdmin = User::factory()->create();
    $siteAdmin->assignRole(['Site Admin']);

    $siteManager = User::factory()->create();
    $siteManager->assignRole(['Site Manager']);

    actingAs($siteAdmin)->get('/admin/organisation')->assertStatus(200);

    actingAs($siteManager)->get('/admin/organisation')->assertStatus(200);
});


test('--- 1.6 Institutional admin, institutional assessor, institutional member CANNOT access Institutions CRUD panel', function () {
    $institutionalAdmin = User::factory()->create();
    $institutionalAdmin->assignRole(['Institutional Admin']);

    $institutionalAssessor = User::factory()->create();
    $institutionalAssessor->assignRole(['Institutional Assessor']);

    $institutionalMember = User::factory()->create();
    $institutionalMember->assignRole(['Institutional Member']);

    actingAs($institutionalAdmin)->get('/admin/organisation')->assertStatus(403);

    actingAs($institutionalAssessor)->get('/admin/organisation')->assertStatus(403);

    actingAs($institutionalMember)->get('/admin/organisation')->assertStatus(403);
});


test('--- 1.7 Site admin, institutional admin, institutional assessor CAN access Portfolios, Projects CRUD panel', function () {
    $siteAdmin = User::factory()->create();
    $siteAdmin->assignRole(['Site Admin']);

    $institutionalAdmin = User::factory()->create();
    $institutionalAdmin->assignRole(['Institutional Admin']);

    $institutionalAssessor = User::factory()->create();
    $institutionalAssessor->assignRole(['Institutional Assessor']);

    actingAs($siteAdmin)->get('/admin/portfolio')->assertStatus(200);
    actingAs($siteAdmin)->get('/admin/project')->assertStatus(200);

    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertStatus(200);
    actingAs($institutionalAdmin)->get('/admin/project')->assertStatus(200);

    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertStatus(200);
    actingAs($institutionalAssessor)->get('/admin/project')->assertStatus(200);
});


test('--- 1.8 Site manager, institutional admin, institutional assessor, institutional member CANNOT access Portfolios, Projects CRUD panel', function () {
    $siteManager = User::factory()->create();
    $siteManager->assignRole(['Site Manager']);

    $institutionalMember = User::factory()->create();
    $institutionalMember->assignRole(['Institutional Member']);

    actingAs($siteManager)->get('/admin/portfolio')->assertStatus(403);
    actingAs($siteManager)->get('/admin/project')->assertStatus(403);

    actingAs($institutionalMember)->get('/admin/portfolio')->assertStatus(403);
    actingAs($institutionalMember)->get('/admin/project')->assertStatus(403);
});


// ********** //


test('2. Check whether different menu items appear for different roles properly')->todo();

test('--- 2.1 Site admin CAN see all menu items', function () {
    $siteAdmin = User::factory()->create();
    $siteAdmin->assignRole(['Site Admin']);

    actingAs($siteAdmin)->get('/admin/organisation')->assertSee("Continents");
    actingAs($siteAdmin)->get('/admin/organisation')->assertSee("Regions");
    actingAs($siteAdmin)->get('/admin/organisation')->assertSee("Countries");

    actingAs($siteAdmin)->get('/admin/organisation')->assertSee("Users");
    actingAs($siteAdmin)->get('/admin/organisation')->assertSee("Admin User Invites");

    actingAs($siteAdmin)->get('/admin/organisation')->assertSee("Red lines");
    actingAs($siteAdmin)->get('/admin/organisation')->assertSee("Principles");
    actingAs($siteAdmin)->get('/admin/organisation')->assertSee("Score tags");

    actingAs($siteAdmin)->get('/admin/organisation')->assertSee("Institutions");
    actingAs($siteAdmin)->get('/admin/organisation')->assertSee("Portfolios");
    actingAs($siteAdmin)->get('/admin/organisation')->assertSee("Initiatives");
});


test('--- 2.2 Site manager CAN see Red lines, Principles, Score tags, Institutions', function () {
    $siteManager = User::factory()->create();
    $siteManager->assignRole(['Site Manager']);
   
    actingAs($siteManager)->get('/admin/organisation')->assertSee("Red lines");
    actingAs($siteManager)->get('/admin/organisation')->assertSee("Principles");
    actingAs($siteManager)->get('/admin/organisation')->assertSee("Score tags");

    // TODO: Institutions menu item not showed yet, to be revised after deciding how to select an institution
    // actingAs($siteManager)->get('/admin/organisation')->assertSee("Institutions");

    // Question: How to check sidebar menu does not contain a keyword? e.g. Continents?
    // actingAs($siteManager)->get('/admin/organisation')->assertSee("Continents");
    // actingAs($siteManager)->get('/admin/organisation')->assertSee("Regions");
    // actingAs($siteManager)->get('/admin/organisation')->assertSee("Countries");

    // actingAs($siteManager)->get('/admin/organisation')->assertSee("Users");
    // actingAs($siteManager)->get('/admin/organisation')->assertSee("Admin User Invites");

    // actingAs($siteManager)->get('/admin/organisation')->assertSee("Portfolios");
    // actingAs($siteManager)->get('/admin/organisation')->assertSee("Initiatives");
});

