<?php

use Illuminate\Support\Facades\Route;
use App\Core\Chatbot\Http\Controllers\DepartmentController;
use App\Core\Chatbot\Http\Controllers\TicketController;
use App\Core\Chatbot\Http\Controllers\CampaignController;
use App\Core\Chatbot\Http\Controllers\AutomationController;
use App\Core\Chatbot\Http\Controllers\AnalyticsController;
use App\Core\Chatbot\Http\Controllers\ContactController;
use App\Core\Chatbot\Http\Controllers\WebhookController;
use App\Core\Chatbot\Http\Controllers\KnowledgeBaseController;
use App\Core\Chatbot\Http\Controllers\VisitorTrackingAdminController;
use App\Core\Chatbot\Http\Controllers\CompanyController;
use App\Core\Chatbot\Http\Controllers\PipelineController;
use App\Core\Chatbot\Http\Controllers\DealController;
use App\Core\Chatbot\Http\Controllers\CannedResponseController;

// Enterprise Admin Routes (new features)
Route::middleware(['web', 'auth'])->prefix('admin/chatbot')->name('admin.chatbot.')->group(function () {
    // Departments
    Route::resource('departments', DepartmentController::class)->names('departments');
    Route::post('departments/{department}/agents', [DepartmentController::class, 'assignAgent'])->name('departments.agents.assign');
    Route::delete('departments/{department}/agents/{agent}', [DepartmentController::class, 'removeAgent'])->name('departments.agents.remove');

    // Tickets
    Route::resource('tickets', TicketController::class)->names('tickets');
    Route::post('tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
    Route::post('tickets/{ticket}/assign', [TicketController::class, 'assign'])->name('tickets.assign');
    Route::post('tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.status');

    // Canned Responses
    Route::get('canned-responses', [CannedResponseController::class, 'index'])->name('canned.index');
    Route::post('canned-responses', [CannedResponseController::class, 'store'])->name('canned.store');
    Route::put('canned-responses/{cannedResponse}', [CannedResponseController::class, 'update'])->name('canned.update');
    Route::delete('canned-responses/{cannedResponse}', [CannedResponseController::class, 'destroy'])->name('canned.destroy');

    // Campaigns
    Route::resource('campaigns', CampaignController::class)->names('campaigns');
    Route::post('campaigns/{campaign}/send', [CampaignController::class, 'send'])->name('campaigns.send');
    Route::post('campaigns/{campaign}/duplicate', [CampaignController::class, 'duplicate'])->name('campaigns.duplicate');

    // Automation
    Route::resource('automations', AutomationController::class)->names('automations');
    Route::post('automations/{automation}/toggle', [AutomationController::class, 'toggle'])->name('automations.toggle');
    Route::post('automations/{automation}/test', [AutomationController::class, 'test'])->name('automations.test');

    // Analytics
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('analytics/realtime', [AnalyticsController::class, 'realtime'])->name('analytics.realtime');
    Route::get('analytics/reports', [AnalyticsController::class, 'reports'])->name('analytics.reports');
    Route::post('analytics/reports', [AnalyticsController::class, 'generateReport'])->name('analytics.reports.generate');

    // Contacts/CRM
    Route::resource('contacts', ContactController::class)->names('contacts');
    Route::post('contacts/{contact}/notes', [ContactController::class, 'addNote'])->name('contacts.notes');
    Route::post('contacts/{contact}/tags', [ContactController::class, 'addTag'])->name('contacts.tags');

    // Webhooks
    Route::resource('webhooks', WebhookController::class)->names('webhooks');
    Route::post('webhooks/{webhook}/test', [WebhookController::class, 'test'])->name('webhooks.test');

    // Knowledge Base (enhanced)
    Route::get('kb', [KnowledgeBaseController::class, 'index'])->name('kb.enterprise');
    Route::post('kb/upload', [KnowledgeBaseController::class, 'upload'])->name('kb.upload');
    Route::delete('kb/{document}', [KnowledgeBaseController::class, 'destroy'])->name('kb.delete');
    Route::post('kb/index-cms', [KnowledgeBaseController::class, 'indexCms'])->name('kb.index-cms');
    Route::get('kb/categories', [KnowledgeBaseController::class, 'categories'])->name('kb.categories');
    Route::post('kb/categories', [KnowledgeBaseController::class, 'storeCategory'])->name('kb.categories.store');
    Route::delete('kb/categories/{category}', [KnowledgeBaseController::class, 'destroyCategory'])->name('kb.categories.destroy');

    // Companies
    Route::resource('companies', CompanyController::class)->names('companies');

    // Pipelines
    Route::resource('pipelines', PipelineController::class)->names('pipelines');
    Route::post('pipelines/{pipeline}/stages', [PipelineController::class, 'storeStage'])->name('pipelines.stages.store');
    Route::put('pipelines/{pipeline}/stages/{stage}', [PipelineController::class, 'updateStage'])->name('pipelines.stages.update');
    Route::post('pipelines/{pipeline}/stages/reorder', [PipelineController::class, 'reorderStages'])->name('pipelines.stages.reorder');
    Route::delete('pipelines/{pipeline}/stages/{stage}', [PipelineController::class, 'destroyStage'])->name('pipelines.stages.destroy');

    // Deals
    Route::get('deals/kanban', [DealController::class, 'kanban'])->name('deals.kanban');
    Route::resource('deals', DealController::class)->names('deals');
    Route::post('deals/{deal}/move', [DealController::class, 'moveStage'])->name('deals.move');
    Route::post('deals/{deal}/status', [DealController::class, 'updateStatus'])->name('deals.status');

    // Visitor Tracking
    Route::prefix('visitors')->name('visitors.')->group(function () {
        Route::get('/', [VisitorTrackingAdminController::class, 'index'])->name('index');
        Route::get('live', [VisitorTrackingAdminController::class, 'live'])->name('live');
        Route::get('live-data', [VisitorTrackingAdminController::class, 'liveData'])->name('live-data');
        Route::get('{visitor}', [VisitorTrackingAdminController::class, 'show'])->name('show');
        Route::delete('{visitor}', [VisitorTrackingAdminController::class, 'deleteVisitor'])->name('delete');
    });
});
