<?php

use App\Http\Controllers\AdditionalAssessmentController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\PrincipleAssessmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(config('backpack.base.route_prefix'));
});

Route::get(config('backpack.base.route_prefix') . '/login', function () {
    return redirect('login');
});


Route::put('organisation/update', [OrganisationController::class, 'update'])->name('organisation.self.update');


// `API` calls for Vue components

Route::apiResource('principle-assessment', PrincipleAssessmentController::class);
Route::get('assessment/{assessment}/principle-assessments', [PrincipleAssessmentController::class, 'index']);
Route::get('assessment/{assessment}/additional-assessments', [AdditionalAssessmentController::class, 'index']);


require __DIR__.'/auth.php';
