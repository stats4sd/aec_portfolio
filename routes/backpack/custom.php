<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

use App\Http\Controllers\Admin\AdditionalCriteriaCrudController;
use App\Http\Controllers\Admin\AdditionalCriteriaScoreTagCrudController;
use App\Http\Controllers\Admin\AssessmentCrudController;
use App\Http\Controllers\Admin\ContinentCrudController;
use App\Http\Controllers\Admin\CountryCrudController;
use App\Http\Controllers\Admin\OrganisationCrudController;
use App\Http\Controllers\Admin\PortfolioCrudController;
use App\Http\Controllers\Admin\PrincipleCrudController;
use App\Http\Controllers\Admin\ProjectCrudController;
use App\Http\Controllers\Admin\RedLineCrudController;
use App\Http\Controllers\Admin\RegionCrudController;
use App\Http\Controllers\Admin\RemovalRequestCrudController;
use App\Http\Controllers\Admin\RoleInviteCrudController;
use App\Http\Controllers\Admin\ScoreTagCrudController;
use App\Http\Controllers\Admin\UserCrudController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\GeneratePdfFileController;
use App\Http\Controllers\GenericDashboardController;
use App\Http\Controllers\MyRoleController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\OrganisationMemberController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SelectedOrganisationController;


Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array)config('backpack.base.web_middleware', 'web'),
        (array)config('backpack.base.middleware_key', 'admin'),
    ),

], function () { // custom admin routes

    Route::get('selected_organisation', [SelectedOrganisationController::class, 'create']);
    Route::post('selected_organisation', [SelectedOrganisationController::class, 'store']);

    Route::get('/', [SelectedOrganisationController::class, 'show']);

    Route::crud('organisation-crud', OrganisationCrudController::class);


    Route::crud('red-line', RedLineCrudController::class);
    Route::crud('principle', PrincipleCrudController::class);
    Route::crud('score-tag', ScoreTagCrudController::class);

    Route::crud('user', UserCrudController::class);
    Route::crud('role-invite', RoleInviteCrudController::class);

    Route::crud('country', CountryCrudController::class);
    Route::crud('continent', ContinentCrudController::class);
    Route::crud('region', RegionCrudController::class);


    Route::get('organisation-members', [OrganisationMemberController::class, 'show']);

    Route::get('organisation/{organisation}/members/create', [OrganisationMemberController::class, 'create'])->name('organisationmembers.create');
    Route::post('organisation/{organisation}/members', [OrganisationMemberController::class, 'store'])->name('organisationmembers.store');
    Route::get('organisation/{organisation}/members/{user}/edit', [OrganisationMemberController::class, 'edit'])->name('organisationmembers.edit');
    Route::put('organisation/{organisation}/members/{user}', [OrganisationMemberController::class, 'update'])->name('organisationmembers.update');
    Route::delete('organisation/{organisation}/members/{user}', [OrganisationMemberController::class, 'destroy'])->name('organisationmembers.destroy');

     Route::get('organisation/export-all', [OrganisationController::class, 'exportAll'])->name('organisation.export-all');

     Route::get('organisation/export-merged', [OrganisationController::class, 'mergeAll']);

        // routes that require a selected organisation
    Route::group([
        'middleware' => ['org.selected'],
    ], function () {

        Route::get('organisation/show', [OrganisationController::class, 'show'])->name('organisation.self.show');
        Route::get('organisation/export', [OrganisationController::class, 'export'])->name('organisation.export');

        Route::crud('portfolio', PortfolioCrudController::class);

        Route::crud('project', ProjectCrudController::class);
        Route::get('project', [ProjectController::class, 'index']);
        Route::get('project/{project}/re-assess', [ProjectCrudController::class, 'reAssess']);


        Route::crud('assessment', AssessmentCrudController::class);
        Route::get('assessment/{assessment}/show', [ProjectCrudController::class, 'showAssessment']);
        Route::get('assessment/{assessment}/assess', [AssessmentController::class, 'assess']);
        Route::post('assessment/{assessment}/finalise', [AssessmentController::class, 'finaliseAssessment']);

        Route::get('generic-dashboard', [GenericDashboardController::class, 'new']);
        Route::get('dashboard', [GenericDashboardController::class, 'show']);

        Route::post('generic-dashboard/enquire', [GenericDashboardController::class, 'enquire']);

        Route::crud('additional-criteria', AdditionalCriteriaCrudController::class);
        Route::get('my-role', [MyRoleController::class, 'show']);
        Route::get('my-role/request-to-leave', [MyRoleController::class, 'requestToLeave']);
        Route::post('my-role/confirm-to-leave', [MyRoleController::class, 'confirmToLeave']);
        Route::get('my-role/request-to-remove-everything', [MyRoleController::class, 'requestToRemoveEverything']);
        Route::post('my-role/confirm-to-remove-everything', [MyRoleController::class, 'confirmToRemoveEverything']);

        Route::crud('removal-request', RemovalRequestCrudController::class);

        Route::get('data-removal/{removeRequest}/cancel', [RemovalRequestCrudController::class, 'cancel']);
        Route::get('data-removal/{removeRequest}/remind', [RemovalRequestCrudController::class, 'remind']);
        Route::get('data-removal/{removeRequest}/confirm', [RemovalRequestCrudController::class, 'confirm']);
        Route::get('data-removal/{removeRequest}/perform', [RemovalRequestCrudController::class, 'perform']);

        Route::post('generatePdf', [GeneratePdfFileController::class, 'generatePdfFile']);

        Route::crud('additional-criteria-score-tag', AdditionalCriteriaScoreTagCrudController::class);

    });


}); // this should be the absolute last line of this file
