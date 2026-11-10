<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Documentation API routes - Fix untuk production
Route::middleware(['auth:web'])->group(function () {
    Route::get('/projects/{project}/documentation/photos', [App\Http\Controllers\DocumentationController::class, 'getPhotos']);
    Route::get('/projects/{project}/documentation/{documentation}/photo', [App\Http\Controllers\DocumentationController::class, 'getPhoto']);
});

// Alternative routes tanpa /api prefix (untuk production compatibility)
Route::group(['prefix' => ''], function () {
    Route::middleware(['auth:web'])->group(function () {
        Route::get('/projects/{project}/documentation/photos', [App\Http\Controllers\DocumentationController::class, 'getPhotos'])->name('api.projects.documentation.photos');
        Route::get('/projects/{project}/documentation/{documentation}/photo', [App\Http\Controllers\DocumentationController::class, 'getPhoto'])->name('api.projects.documentation.photo');
    });
});