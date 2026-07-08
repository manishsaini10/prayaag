<?php

use App\Http\Controllers\Api\Popup\PopupApiController;
use App\Http\Controllers\Api\Popup\PopupTrackingController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/popup')->name('api.popup.')->middleware(['api', 'throttle:60,1'])->group(function () {
    // Public endpoints
    Route::get('/render/{popup}', [PopupApiController::class, 'render'])->name('render');
    Route::get('/active', [PopupApiController::class, 'active'])->name('active');

    // Tracking endpoints (no auth required for analytics)
    Route::post('/track', [PopupTrackingController::class, 'track'])->name('track');
    Route::post('/lead', [PopupTrackingController::class, 'lead'])->name('lead');
    Route::post('/conversion', [PopupTrackingController::class, 'conversion'])->name('conversion');

    // Authenticated API endpoints
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('popups', PopupApiController::class)->except(['create', 'edit']);
        Route::post('/popups/{popup}/duplicate', [PopupApiController::class, 'duplicate'])->name('duplicate');
        Route::post('/popups/{popup}/publish', [PopupApiController::class, 'publish'])->name('publish');
        Route::post('/popups/{popup}/unpublish', [PopupApiController::class, 'unpublish'])->name('unpublish');
        Route::get('/analytics/{popup}', [PopupApiController::class, 'analytics'])->name('analytics');
        Route::get('/leads/{popup}', [PopupApiController::class, 'leads'])->name('leads');
    });
});
