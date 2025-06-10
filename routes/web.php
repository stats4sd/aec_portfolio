<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\Admin\ProjectCrudController;
use App\Http\Controllers\PrincipleAssessmentController;
use App\Http\Controllers\AdditionalAssessmentController;

Route::get('/', function () {
    return redirect(config('backpack.base.route_prefix'));
});

Route::get(config('backpack.base.route_prefix') . '/login', function () {
    return redirect('login');
});


Route::post('organisation/update', [OrganisationController::class, 'update'])->name('organisation.self.update');




// `API` calls for Vue components

Route::apiResource('principle-assessment', PrincipleAssessmentController::class);
Route::apiResource('additional-assessment', AdditionalAssessmentController::class);

Route::get('assessment/{assessment}/principle-assessments', [PrincipleAssessmentController::class, 'index']);
Route::get('assessment/{assessment}/additional-assessments', [AdditionalAssessmentController::class, 'index']);


Route::post('exchange-rate', [ExchangeRateController::class, 'index']);


// add a route to store project Id in session
Route::post('store-project-id-in-session', [ProjectController::class, 'storeProjectIdInSession']);


require __DIR__ . '/auth.php';

Route::middleware('auth')->group(function () {
    Route::mediaLibrary();
});
