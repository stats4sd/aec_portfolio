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
use App\Http\Controllers\Admin\HelpTextEntryCrudController;
use App\Http\Controllers\Admin\UserFeedbackTypeCrudController;
use App\Http\Controllers\Admin\InitiativeCategoryCrudController;
use App\Http\Controllers\Admin\InstitutionTypeCrudController;
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
use App\Http\Controllers\Admin\UserFeedbackCrudController;
use App\Http\Controllers\Admin\RevisionCrudController;
use App\Http\Controllers\Admin\CustomScoreTagCrudController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\GeneratePdfFileController;
use App\Http\Controllers\GenericDashboardController;
use App\Http\Controllers\MyRoleController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\OrganisationMemberController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SelectedOrganisationController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\UserFeedbackController;
use Backpack\CRUD\app\Http\Controllers\MyAccountController;


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


    Route::crud('red-flag', RedLineCrudController::class);
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

        // redirect route for saving org settings (used to reload page if has_additional_assessment is changed)
        Route::get('organisation/saved', [OrganisationController::class, 'saved'])->name('organisation.saved');


        Route::get('organisation/export', [OrganisationController::class, 'export'])->name('organisation.export');
        Route::post('organisation/store-tab', [OrganisationController::class, 'storeTab']);


        Route::crud('portfolio', PortfolioCrudController::class);
        Route::get('portfolio', function () {
            return redirect("admin/organisation/show");
        })->name('portfolio.index');

        Route::crud('project', ProjectCrudController::class);
        Route::get('project', [ProjectController::class, 'index']);
        Route::post('project/{id}/reassess', [ProjectCrudController::class, 'reAssess']);
        Route::post('project/{project}/duplicate', [ProjectCrudController::class, 'duplicate']);

        Route::post('session/store', [SessionController::class, 'store']);
        Route::post('session/reset', [SessionController::class, 'reset']);


        Route::crud('assessment', AssessmentCrudController::class);
        Route::get('assessment/{assessment}/show', [ProjectCrudController::class, 'showAssessment']);
        Route::get('assessment/{assessment}/assess', [AssessmentController::class, 'assess']);
        Route::get('assessment/{assessment}/assess-custom', [AssessmentController::class, 'assessCustom']);
        Route::post('assessment/{assessment}/finalise', [AssessmentController::class, 'finaliseAssessment']);

        Route::post('additional/{assessment}/finalise', [AssessmentController::class, 'finaliseAssessmentCustom']);


        Route::get('dashboard', [GenericDashboardController::class, 'show']);

        Route::post('dashboard/enquire', [GenericDashboardController::class, 'enquire']);

        Route::crud('additional-criteria', AdditionalCriteriaCrudController::class);
        Route::get('additional-criteria', function () {
            return redirect("admin/organisation/show#additional-criteria");
        })->name('additional-criteria.index');

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
        Route::post('project/{project}/generate-pdf', [GeneratePdfFileController::class, 'generateInitiativeSummary']);
        Route::post('assessment/{assessment}/generate-pdf', [GeneratePdfFileController::class, 'generateAssessmentSummary']);


        // download files
        Route::get('files/{filename}', [GeneratePdfFileController::class, 'download'])
            ->where('filename', '.*');

        Route::crud('additional-criteria-score-tag', AdditionalCriteriaScoreTagCrudController::class);
        Route::view('support', 'support');

        Route::get('edit-account-info', [MyAccountController::class, 'getAccountInfoForm'])->name('backpack.account.info');
    });


    Route::post('edit-account-info', [MyAccountController::class, 'postAccountInfoForm'])->name('backpack.account.info.store');
    Route::post('change-password', [MyAccountController::class,'postChangePasswordForm'])->name('backpack.account.password');
    Route::crud('initiative-category', InitiativeCategoryCrudController::class);
    Route::crud('institution-type', InstitutionTypeCrudController::class);
    Route::crud('user-feedback', UserFeedbackCrudController::class);
    Route::post('user-feedback', [UserFeedbackController::class, 'store']);
    Route::crud('feedback-type', UserFeedbackTypeCrudController::class);

    Route::crud('revision', RevisionCrudController::class);
    Route::crud('custom-score-tag', CustomScoreTagCrudController::class);
    Route::crud('help-text-entry', HelpTextEntryCrudController::class);
    Route::get('help-text-entry/find/{location}', [HelpTextEntryCrudController::class, 'find']);
});

Route::get('project/{id}/show-as-pdf', [ProjectCrudController::class, 'show'])
    ->middleware('auth.basic')
    ->name('project.show-as-pdf');

Route::get('assessment/{id}/show-as-pdf', [ProjectCrudController::class, 'showAssessment'])
    ->middleware('auth.basic')
    ->name('assessment.show-as-pdf');
