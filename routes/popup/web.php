<?php

use Illuminate\Support\Facades\Route;

// Public popup routes (for frontend rendering)
Route::prefix('__popup')->name('popup.')->group(function () {
    // These are handled by the PopupTrackingController in the API routes
    // Public JS/CSS assets are served from /js/popup-builder/ and /css/popup-builder/
});
