<?php

use App\Http\Controllers\PrincipleAssessmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(config('backpack.base.route_prefix'));
});

Route::get(config('backpack.base.route_prefix') . '/login', function () {
    return redirect('login');
});


// `API` calls for Vue components

Route::apiResource('principle-assessment', PrincipleAssessmentController::class);
Route::get('assessment/{assessment}/principle-assessments', [PrincipleAssessmentController::class, 'index']);

require __DIR__.'/auth.php';
