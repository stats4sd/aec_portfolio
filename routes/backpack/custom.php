<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

use App\Http\Controllers\Admin\OrganisationCrudController;
use App\Http\Controllers\Admin\PrincipleCrudController;
use App\Http\Controllers\Admin\ProjectCrudController;
use App\Http\Controllers\Admin\RedLineCrudController;
use App\Http\Controllers\Admin\RoleInviteCrudController;
use App\Http\Controllers\Admin\ScoreTagCrudController;
use App\Http\Controllers\Admin\UserCrudController;
use App\Http\Controllers\OrganisationMemberController;

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array)config('backpack.base.web_middleware', 'web'),
        (array)config('backpack.base.middleware_key', 'admin')
    ),

], function () { // custom admin routes
    Route::crud('project', ProjectCrudController::class);
    Route::crud('red-line', RedLineCrudController::class);
    Route::crud('principle', PrincipleCrudController::class);
    Route::crud('score-tag', ScoreTagCrudController::class);
    Route::crud('organisation', OrganisationCrudController::class);
    Route::crud('user', UserCrudController::class);
    Route::crud('role-invite', RoleInviteCrudController::class);


    Route::get('organisation/{organisation}/members/create', [OrganisationMemberController::class, 'create'])->name('organisationmembers.create');
    Route::post('organisation/{organisation}/members', [OrganisationMemberController::class, 'store'])->name('organisationmembers.store');
    Route::get('organisation/{organisation}/members/{user}/edit', [OrganisationMemberController::class, 'edit'])->name('organisationmembers.edit');
    Route::put('organisation/{organisation}/members/{user}', [OrganisationMemberController::class, 'update'])->name('organisationmembers.update');
    Route::delete('organisation/{organisation}/members/{user}', [OrganisationMemberController::class, 'destroy'])->name('organisationmembers.destroy');

    Route::get('dashboard', function() {
        if(Auth::user()->organisations()->count() > 1 || Auth::user()->hasRole('admin')) {
            return redirect(backpack_url('organisations'));
        }

        if(Auth::user()->organisations()->count() === 1) {
            return redirect(backpack_url('organisation/'.Auth::user()->organisations->first()->id.'/show'));
        }



        abort(403, "It looks like you are not a member of any organisation, and are not a site admin. If you think this is incorrect, please contact support@stats4sd.org");
    })->name('backpack.dashboard');
    Route::get('/', 'AdminController@redirect')->name('backpack');

}); // this should be the absolute last line of this file
