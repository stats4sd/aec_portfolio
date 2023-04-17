<?php

use App\Models\User;
use function Pest\Laravel\{actingAs};


beforeEach(function () {
    // Prepare something once before any of this file's tests run...
});


test('1. Site admin CAN access Continents, Regions, Countries, Users, Admin User Invites CRUD panels', function () {
    $siteAdmin = $this->setupSiteAdminUser();

    actingAs($siteAdmin)->get('/admin/continent')->assertStatus(200);
    actingAs($siteAdmin)->get('/admin/region')->assertStatus(200);
    actingAs($siteAdmin)->get('/admin/country')->assertStatus(200);
    actingAs($siteAdmin)->get('/admin/user')->assertStatus(200);
    actingAs($siteAdmin)->get('/admin/role-invite')->assertStatus(200);
});


test('2. Site manager, institutional admin, institutional assessor, institutional member CANNOT access Continents, Regions, Countries, Users, Admin User Invites CRUD panels', function () {
    $siteManager = $this->setupSiteManagerUser();
    $institutionalAdmin = $this->setupInstitutionalAdminUser();
    $institutionalAssessor = $this->setupInstitutionalAssessorUser();
    $institutionalMember = $this->setupInstitutionalMemberUser();

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


test('3. Site admin, site manager CAN access Red Lines, Principles, Score Tags CRUD panels', function () {
    $siteAdmin = $this->setupSiteAdminUser();
    $siteManager = $this->setupSiteManagerUser();

    actingAs($siteAdmin)->get('/admin/red-line')->assertStatus(200);
    actingAs($siteAdmin)->get('/admin/principle')->assertStatus(200);
    actingAs($siteAdmin)->get('/admin/score-tag')->assertStatus(200);

    actingAs($siteManager)->get('/admin/red-line')->assertStatus(200);
    actingAs($siteManager)->get('/admin/principle')->assertStatus(200);
    actingAs($siteManager)->get('/admin/score-tag')->assertStatus(200);
});


test('4. Institutional admin, institutional assessor, institutional member CANNOT access Red Lines, Principles, Score Tags CRUD panels', function () {
    $institutionalAdmin = $this->setupInstitutionalAdminUser();
    $institutionalAssessor = $this->setupInstitutionalAssessorUser();
    $institutionalMember = $this->setupInstitutionalMemberUser();

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


test('5. Site admin, site manager CAN access Institutions CRUD panel', function () {
    $siteAdmin = $this->setupSiteAdminUser();
    $siteManager = $this->setupSiteManagerUser();

    actingAs($siteAdmin)->get('/admin/organisation')->assertStatus(200);

    actingAs($siteManager)->get('/admin/organisation')->assertStatus(200);
});


test('6. Institutional admin, institutional assessor, institutional member CANNOT access Institutions CRUD panel', function () {
    $institutionalAdmin = $this->setupInstitutionalAdminUser();
    $institutionalAssessor = $this->setupInstitutionalAssessorUser();
    $institutionalMember = $this->setupInstitutionalMemberUser();

    actingAs($institutionalAdmin)->get('/admin/organisation')->assertStatus(403);

    actingAs($institutionalAssessor)->get('/admin/organisation')->assertStatus(403);

    actingAs($institutionalMember)->get('/admin/organisation')->assertStatus(403);
});


test('7. Site admin, institutional admin, institutional assessor CAN access Portfolios, Projects CRUD panel', function () {
    $siteAdmin = $this->setupSiteAdminUser();
    $institutionalAdmin = $this->setupInstitutionalAdminUser();
    $institutionalAssessor = $this->setupInstitutionalAssessorUser();

    actingAs($siteAdmin)->get('/admin/portfolio')->assertStatus(200);
    actingAs($siteAdmin)->get('/admin/project')->assertStatus(200);

    actingAs($institutionalAdmin)->get('/admin/portfolio')->assertStatus(200);
    actingAs($institutionalAdmin)->get('/admin/project')->assertStatus(200);

    actingAs($institutionalAssessor)->get('/admin/portfolio')->assertStatus(200);
    actingAs($institutionalAssessor)->get('/admin/project')->assertStatus(200);
});


test('8. Site manager, institutional admin, institutional assessor, institutional member CANNOT access Portfolios, Projects CRUD panel', function () {
    $siteManager = $this->setupSiteManagerUser();
    $institutionalMember = $this->setupInstitutionalMemberUser();

    actingAs($siteManager)->get('/admin/portfolio')->assertStatus(403);
    actingAs($siteManager)->get('/admin/project')->assertStatus(403);

    actingAs($institutionalMember)->get('/admin/portfolio')->assertStatus(403);
    actingAs($institutionalMember)->get('/admin/project')->assertStatus(403);
});

