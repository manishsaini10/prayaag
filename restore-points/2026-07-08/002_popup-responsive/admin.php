<?php

use App\Http\Controllers\Admin\PopupController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/popup-builder')->name('admin.popup-builder.')->middleware(['web', 'auth'])->group(function () {
    Route::get('/', [PopupController::class, 'index'])->name('dashboard');
    Route::get('/create', [PopupController::class, 'create'])->name('create');
    Route::post('/', [PopupController::class, 'store'])->name('store');
    Route::post('/upload', [PopupController::class, 'upload'])->name('upload');
    Route::get('/{id}/edit', [PopupController::class, 'edit'])->name('edit');
    Route::put('/{id}', [PopupController::class, 'update'])->name('update');
    Route::delete('/{id}', [PopupController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/duplicate', [PopupController::class, 'duplicate'])->name('duplicate');
    Route::post('/{id}/publish', [PopupController::class, 'publish'])->name('publish');
    Route::post('/{id}/unpublish', [PopupController::class, 'unpublish'])->name('unpublish');
    Route::get('/{id}/analytics', [PopupController::class, 'analytics'])->name('analytics');
    Route::get('/{id}/leads', [PopupController::class, 'leads'])->name('leads');
    Route::get('/{id}/preview', [PopupController::class, 'preview'])->name('preview');
});
