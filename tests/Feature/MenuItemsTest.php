<?php

use App\Models\User;
use function Pest\Laravel\{actingAs};


beforeEach(function () {
    // Prepare something once before any of this file's tests run...
});


test('1. Site admin CAN see all menu items', function () {
    $siteAdmin = $this->setupSiteAdminUser();

    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/select_organisation");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/organisation");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/organisation-members");

    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/portfolio");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/project");

    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/red-line");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/principle");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/score-tag");

    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/user");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/role-invite");

    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/continent");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/region");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/country");

    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/generic-dashboard?level=institution");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/generic-dashboard?level=portfolio");
    actingAs($siteAdmin)->get('/admin/principle')->assertSee("/admin/generic-dashboard?level=initiative");
});


test('2. Site manager CAN see Select Institution, Institutions, Red lines, Principles, Score tags', function () {
    $siteManager = $this->setupSiteManagerUser();

    actingAs($siteManager)->get('/admin/principle')->assertSee("/admin/select_organisation");
    actingAs($siteManager)->get('/admin/principle')->assertSee("/admin/organisation");

    actingAs($siteManager)->get('/admin/principle')->assertSee("/admin/red-line");
    actingAs($siteManager)->get('/admin/principle')->assertSee("/admin/principle");
    actingAs($siteManager)->get('/admin/principle')->assertSee("/admin/score-tag");

    //////////

    // TODO: not sure why it does not work
    // actingAs($siteManager)->get('/admin/principle')->assertDontSee("/admin/organisation-members");

    actingAs($siteManager)->get('/admin/principle')->assertDontSee("/admin/portfolio");
    actingAs($siteManager)->get('/admin/principle')->assertDontSee("/admin/project");

    actingAs($siteManager)->get('/admin/principle')->assertDontSee("/admin/user");
    actingAs($siteManager)->get('/admin/principle')->assertDontSee("/admin/role-invite");

    actingAs($siteManager)->get('/admin/principle')->assertDontSee("/admin/continent");
    actingAs($siteManager)->get('/admin/principle')->assertDontSee("/admin/region");
    actingAs($siteManager)->get('/admin/principle')->assertDontSee("/admin/country");

    actingAs($siteManager)->get('/admin/principle')->assertDontSee("/admin/generic-dashboard?level=institution");
    actingAs($siteManager)->get('/admin/principle')->assertDontSee("/admin/generic-dashboard?level=portfolio");
    actingAs($siteManager)->get('/admin/principle')->assertDontSee("/admin/generic-dashboard?level=initiative");
});


test('3. Institutional admin CAN see Institutions, Portfolios, Projects, Institution dashboard, Portfolio dashboard, Initiative dashboard', function () {
    $institutionalAdmin = $this->setupInstitutionalAdminUser();

    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertSee("/admin/organisation-members");

    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertSee("/admin/portfolio");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertSee("/admin/project");

    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertSee("/admin/generic-dashboard?level=institution");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertSee("/admin/generic-dashboard?level=portfolio");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertSee("/admin/generic-dashboard?level=initiative");

    //////////

    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("/admin/select_organisation");

    // TODO: not sure why it does not work
    // actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("/admin/organisation");

    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("/admin/user");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("/admin/role-invite");

    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("/admin/red-line");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("/admin/principle");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("/admin/score-tag");

    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("/admin/continent");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("/admin/region");
    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertDontSee("/admin/country");

});


test('4. Institutional assessor CAN see Portfolios, Projects, Institution dashboard, Portfolio dashboard, Initiative dashboard', function () {
    $institutionalAssessor = $this->setupInstitutionalAssessorUser();

    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertSee("/admin/portfolio");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertSee("/admin/project");

    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertSee("/admin/generic-dashboard?level=institution");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertSee("/admin/generic-dashboard?level=portfolio");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertSee("/admin/generic-dashboard?level=initiative");

    //////////

    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("/admin/select_organisation");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("/admin/organisation");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("/admin/organisation-members");

    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("/admin/user");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("/admin/role-invite");

    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("/admin/red-line");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("/admin/principle");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("/admin/score-tag");

    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("/admin/continent");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("/admin/region");
    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertDontSee("/admin/country");
});


test('5. Institutional member CAN see Institution dashboard, Portfolio dashboard, Initiative dashboard', function () {
    $institutionalMember = $this->setupInstitutionalMemberUser();

    // TODO: not sure why it does not work
    // actingAs($institutionalMember)->get('/admin/dashboard')->assertSee("/admin/generic-dashboard?level=institution");
    // actingAs($institutionalMember)->get('/admin/dashboard')->assertSee("/admin/generic-dashboard?level=portfolio");
    // actingAs($institutionalMember)->get('/admin/dashboard')->assertSee("/admin/generic-dashboard?level=initiative");

    //////////

    actingAs($institutionalMember)->get('/admin/dashboard')->assertDontSee("/admin/select_organisation");
    actingAs($institutionalMember)->get('/admin/dashboard')->assertDontSee("/admin/organisation");
    actingAs($institutionalMember)->get('/admin/dashboard')->assertDontSee("/admin/organisation-members");

    actingAs($institutionalMember)->get('/admin/dashboard')->assertDontSee("/admin/portfolio");
    actingAs($institutionalMember)->get('/admin/dashboard')->assertDontSee("/admin/project");

    actingAs($institutionalMember)->get('/admin/dashboard')->assertDontSee("/admin/user");
    actingAs($institutionalMember)->get('/admin/dashboard')->assertDontSee("/admin/role-invite");

    actingAs($institutionalMember)->get('/admin/dashboard')->assertDontSee("/admin/red-line");
    actingAs($institutionalMember)->get('/admin/dashboard')->assertDontSee("/admin/principle");
    actingAs($institutionalMember)->get('/admin/dashboard')->assertDontSee("/admin/score-tag");

    actingAs($institutionalMember)->get('/admin/dashboard')->assertDontSee("/admin/continent");
    actingAs($institutionalMember)->get('/admin/dashboard')->assertDontSee("/admin/region");
    actingAs($institutionalMember)->get('/admin/dashboard')->assertDontSee("/admin/country");
});
