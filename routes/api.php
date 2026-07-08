<?php

use App\Http\Controllers\Cms\PageApiController;
use App\Http\Controllers\Cms\WidgetApiController;
use Illuminate\Support\Facades\Route;

// Admin builder API (single-site). Auth (session/default guard) + per-route
// permission gating.
Route::middleware('auth')->group(function () {
    Route::get('widgets', [WidgetApiController::class, 'index'])
        ->middleware('permission:pages.view');

    Route::get('pages', [PageApiController::class, 'index'])
        ->middleware('permission:pages.view');
    Route::post('pages', [PageApiController::class, 'store'])
        ->middleware('permission:pages.create');
    Route::get('pages/{page}', [PageApiController::class, 'show'])
        ->middleware('permission:pages.view');
    Route::put('pages/{page}', [PageApiController::class, 'update'])
        ->middleware('permission:pages.update');
    Route::delete('pages/{page}', [PageApiController::class, 'destroy'])
        ->middleware('permission:pages.delete');
    Route::put('pages/{page}/tree', [PageApiController::class, 'syncTree'])
        ->middleware('permission:pages.update');
});
