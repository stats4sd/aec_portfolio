<?php

use App\Models\User;
use function Pest\Laravel\{actingAs};


beforeEach(function () {
    // Prepare something once before any of this file's tests run...
});


test('1. Site admin CAN see all menu items', function () {
    $siteAdmin = User::factory()->create();
    $siteAdmin->assignRole(['Site Admin']);

    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/continent");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/region");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/country");

    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/user");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/role-invite");

    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/red-line");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/principle");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/score-tag");

    // to be revised
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/dashboard");

    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/portfolio");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/project");

    actingAs($siteAdmin)->get('/admin/principle')->assertSee("admin/new_dashboard?level=institution");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("admin/new_dashboard?level=portfolio");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("admin/new_dashboard?level=project");
});


test('2. Site manager CAN see Red lines, Principles, Score tags, Institutions', function () {
    $siteManager = User::factory()->create();
    $siteManager->assignRole(['Site Manager']);
   
    actingAs($siteManager)->get('/admin/principle')->assertSee("/admin/red-line");
    actingAs($siteManager)->get('/admin/principle')->assertSee("/admin/principle");
    actingAs($siteManager)->get('/admin/principle')->assertSee("/admin/score-tag");


    // TODO: Institutions menu item not showed yet, to be revised after deciding how to select an institution
    // actingAs($siteManager)->get('/admin/organisation')->assertSee("Institutions");

    actingAs($siteManager)->get('/admin/principle')->assertDontSee("/admin/continent");
    actingAs($siteManager)->get('/admin/principle')->assertDontSee("/admin/region");
    actingAs($siteManager)->get('/admin/principle')->assertDontSee("/admin/country");

    actingAs($siteManager)->get('/admin/principle')->assertDontSee("/admin/user");
    actingAs($siteManager)->get('/admin/principle')->assertDontSee("/admin/role-invite");

    actingAs($siteManager)->get('/admin/principle')->assertDontSee("/admin/portfolio");
    actingAs($siteManager)->get('/admin/principle')->assertDontSee("/admin/project");

    actingAs($siteManager)->get('/admin/principle')->assertDontSee("admin/new_dashboard?level=institution");
    actingAs($siteManager)->get('/admin/principle')->assertDontSee("admin/new_dashboard?level=portfolio");
    actingAs($siteManager)->get('/admin/principle')->assertDontSee("admin/new_dashboard?level=project");
});


test('3. Institutional admin CAN see Institutions, Portfolios, Projects, Institution dashboard, Portfolio dashboard, Initiative dashboard', function () {
    $institutionalAdmin = User::factory()->create();
    $institutionalAdmin->assignRole(['Institutional Admin']);

    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertSee("/admin/portfolio");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertSee("/admin/project");

    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertSee("admin/new_dashboard?level=institution");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertSee("admin/new_dashboard?level=portfolio");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertSee("admin/new_dashboard?level=project");

    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("/admin/red-line");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("/admin/principle");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("/admin/score-tag");

    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("/admin/continent");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("/admin/region");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("/admin/country");

    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("/admin/user");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("/admin/role-invite");
});


test('4. Institutional assessor CAN see Portfolios, Projects, Institution dashboard, Portfolio dashboard, Initiative dashboard', function () {
    $institutionalAssessor = User::factory()->create();
    $institutionalAssessor->assignRole(['Institutional Assessor']);

    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertSee("/admin/portfolio");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertSee("/admin/project");

    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertSee("admin/new_dashboard?level=institution");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertSee("admin/new_dashboard?level=portfolio");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertSee("admin/new_dashboard?level=project");

    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("/admin/red-line");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("/admin/principle");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("/admin/score-tag");

    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("/admin/continent");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("/admin/region");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("/admin/country");

    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("/admin/user");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("/admin/role-invite");
});


test('5. Institutional member CAN see Institution dashboard, Portfolio dashboard, Initiative dashboard', function () {
    $institutionalMember = User::factory()->create();
    $institutionalMember->assignRole(['Institutional Member']);

    actingAs($institutionalMember)->get('/admin/organisation')->assertSee("admin/new_dashboard?level=institution");
    actingAs($institutionalMember)->get('/admin/organisation')->assertSee("admin/new_dashboard?level=portfolio");
    actingAs($institutionalMember)->get('/admin/organisation')->assertSee("admin/new_dashboard?level=project");

    actingAs($institutionalMember)->get('/admin/organisation')->assertDontSee("/admin/portfolio");
    actingAs($institutionalMember)->get('/admin/organisation')->assertDontSee("/admin/project");

    actingAs($institutionalMember)->get('/admin/organisation')->assertDontSee("/admin/red-line");
    actingAs($institutionalMember)->get('/admin/organisation')->assertDontSee("/admin/principle");
    actingAs($institutionalMember)->get('/admin/organisation')->assertDontSee("/admin/score-tag");

    actingAs($institutionalMember)->get('/admin/organisation')->assertDontSee("/admin/continent");
    actingAs($institutionalMember)->get('/admin/organisation')->assertDontSee("/admin/region");
    actingAs($institutionalMember)->get('/admin/organisation')->assertDontSee("/admin/country");

    actingAs($institutionalMember)->get('/admin/organisation')->assertDontSee("/admin/user");
    actingAs($institutionalMember)->get('/admin/organisation')->assertDontSee("/admin/role-invite");
});
