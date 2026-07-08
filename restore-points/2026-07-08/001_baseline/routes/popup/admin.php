<?php

use App\Http\Livewire\Popup\PopupDashboard;
use App\Http\Livewire\Popup\PopupEditor;
use App\Http\Livewire\Popup\PopupSettings;
use App\Http\Livewire\Popup\PopupAnalytics;
use App\Http\Livewire\Popup\PopupLeads;
use App\Http\Livewire\Popup\PopupTemplates;
use App\Http\Livewire\Popup\PopupAbTesting;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/popup-builder')->name('admin.popup-builder.')->middleware(['web', 'auth'])->group(function () {
    // Dashboard
    Route::get('/', PopupDashboard::class)->name('dashboard');

    // CRUD
    Route::get('/create/{template?}', PopupEditor::class)->name('create');
    Route::get('/edit/{id}', PopupEditor::class)->name('edit');

    // Settings
    Route::get('/settings/{id}', PopupSettings::class)->name('settings');

    // Analytics
    Route::get('/analytics/{id}', PopupAnalytics::class)->name('analytics');

    // Leads
    Route::get('/leads/{id}', PopupLeads::class)->name('leads');

    // Templates
    Route::get('/templates', PopupTemplates::class)->name('templates');

    // A/B Testing
    Route::get('/ab-testing', PopupAbTesting::class)->name('ab-testing');
});
