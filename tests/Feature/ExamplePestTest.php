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

    actingAs($siteAdmin)->get('/admin/principle')->assertSee("Continents");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("Regions");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("Countries");

    actingAs($siteAdmin)->get('/admin/principle')->assertSee("Users");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("Admin User Invites");

    actingAs($siteAdmin)->get('/admin/principle')->assertSee("Red lines");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("Principles");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("Score tags");

    actingAs($siteAdmin)->get('/admin/principle')->assertSee("Institutions");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("Portfolios");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("Initiatives");

    actingAs($siteAdmin)->get('/admin/principle')->assertSee("Institution dashboard");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("Portfolio dashboard");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("Initiative dashboard");
});


test('--- 2.2 Site manager CAN see Red lines, Principles, Score tags, Institutions', function () {
    $siteManager = User::factory()->create();
    $siteManager->assignRole(['Site Manager']);
   
    actingAs($siteManager)->get('/admin/principle')->assertSee("Red lines");
    actingAs($siteManager)->get('/admin/principle')->assertSee("Principles");
    actingAs($siteManager)->get('/admin/principle')->assertSee("Score tags");

    // TODO: Institutions menu item not showed yet, to be revised after deciding how to select an institution
    // actingAs($siteManager)->get('/admin/organisation')->assertSee("Institutions");

    actingAs($siteManager)->get('/admin/principle')->assertDontSee("Continents");
    actingAs($siteManager)->get('/admin/principle')->assertDontSee("Regions");
    actingAs($siteManager)->get('/admin/principle')->assertDontSee("Countries");

    actingAs($siteManager)->get('/admin/principle')->assertDontSee("Users");
    actingAs($siteManager)->get('/admin/principle')->assertDontSee("Admin User Invites");

    actingAs($siteManager)->get('/admin/principle')->assertDontSee("Portfolios");
    actingAs($siteManager)->get('/admin/principle')->assertDontSee("Initiatives");

    actingAs($siteManager)->get('/admin/principle')->assertDontSee("Institution dashboard");
    actingAs($siteManager)->get('/admin/principle')->assertDontSee("Portfolio dashboard");
    actingAs($siteManager)->get('/admin/principle')->assertDontSee("Initiative dashboard");
});


test('--- 2.3 Institutional admin CAN see Institutions, Portfolios, Projects, Institution dashboard, Portfolio dashboard, Initiative dashboard', function () {
    $institutionalAdmin = User::factory()->create();
    $institutionalAdmin->assignRole(['Institutional Admin']);

    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertSee("Portfolios");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertSee("Initiatives");

    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertSee("Institution dashboard");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertSee("Portfolio dashboard");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertSee("Initiative dashboard");

    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("Red lines");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("Principles");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("Score tags");

    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("Continents");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("Regions");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("Countries");

    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("Users");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("Admin User Invites");
});


test('--- 2.4 Institutional assessor CAN see Portfolios, Projects, Institution dashboard, Portfolio dashboard, Initiative dashboard', function () {
    $institutionalAssessor = User::factory()->create();
    $institutionalAssessor->assignRole(['Institutional Assessor']);

    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertSee("Portfolios");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertSee("Initiatives");

    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertSee("Institution dashboard");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertSee("Portfolio dashboard");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertSee("Initiative dashboard");

    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("Red lines");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("Principles");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("Score tags");

    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("Continents");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("Regions");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("Countries");

    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("Users");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("Admin User Invites");
});


test('--- 2.5 Institutional member CAN see Institution dashboard, Portfolio dashboard, Initiative dashboard', function () {
    $institutionalMember = User::factory()->create();
    $institutionalMember->assignRole(['Institutional Member']);

    actingAs($institutionalMember)->get('/admin/organisation')->assertSee("Institution dashboard");
    actingAs($institutionalMember)->get('/admin/organisation')->assertSee("Portfolio dashboard");
    actingAs($institutionalMember)->get('/admin/organisation')->assertSee("Initiative dashboard");

    actingAs($institutionalMember)->get('/admin/organisation')->assertDontSee("Portfolios");
    actingAs($institutionalMember)->get('/admin/organisation')->assertDontSee("Initiatives");

    actingAs($institutionalMember)->get('/admin/organisation')->assertDontSee("Red lines");
    actingAs($institutionalMember)->get('/admin/organisation')->assertDontSee("Principles");
    actingAs($institutionalMember)->get('/admin/organisation')->assertDontSee("Score tags");

    actingAs($institutionalMember)->get('/admin/organisation')->assertDontSee("Continents");
    actingAs($institutionalMember)->get('/admin/organisation')->assertDontSee("Regions");
    actingAs($institutionalMember)->get('/admin/organisation')->assertDontSee("Countries");

    actingAs($institutionalMember)->get('/admin/organisation')->assertDontSee("Users");
    actingAs($institutionalMember)->get('/admin/organisation')->assertDontSee("Admin User Invites");
});
