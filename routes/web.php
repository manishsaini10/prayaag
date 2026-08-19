<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EditorController;
use App\Http\Controllers\Admin\WidgetBuilderController;
use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\Admin\InboxController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\MediaLibraryController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Cms\EnquiryController;
use App\Http\Controllers\Cms\FormController as PublicFormController;
use App\Http\Controllers\Cms\JobApplicationController;
use App\Http\Controllers\Cms\PageController;
use App\Http\Controllers\Cms\SitemapController;
use App\Http\Controllers\Cms\SubscriberController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SiteSearchController;
use App\Http\Controllers\Admin\AdminChatbotController;
use App\Http\Controllers\Admin\AdminPreChatFormController;
use App\Http\Controllers\Cms\PublicChatbotController;
use App\Http\Controllers\Admin\AdminTestimonialController;
use App\Http\Controllers\Admin\VideoTestimonialController;
use App\Http\Controllers\Admin\VideoTestimonialAnalyticsController;
use App\Http\Controllers\VideoTestimonialSubmissionController;
use App\Http\Controllers\Cms\PublicTestimonialController;
use Illuminate\Support\Facades\Route;

// --- Authentication ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 2FA Challenge (user must be logged in, but no admin layout)
Route::middleware('auth')->group(function () {
    Route::get('/2fa/challenge', [\App\Http\Controllers\Auth\TwoFactorController::class, 'showChallenge'])->name('2fa.challenge');
    Route::post('/2fa/verify', [\App\Http\Controllers\Auth\TwoFactorController::class, 'verify'])->name('2fa.verify');
});

// --- Admin (authenticated + 2FA-enforced) ---
// require.2fa: redirects admin/super-admin to 2FA setup if not yet configured,
//              and to 2FA challenge if session has not been verified this session.
Route::middleware(['auth', 'require.2fa'])->group(function () {
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/ai-assist', [\App\Http\Controllers\Admin\AiContentAssistController::class, 'generate'])->name('admin.ai-assist');

    // Page editor
    Route::get('/admin/pages', [EditorController::class, 'index'])->name('admin.pages.index');
    Route::get('/admin/pages/builder', [EditorController::class, 'builder'])->name('admin.pages.builder');
    Route::get('/admin/pages/{page}/edit', [EditorController::class, 'edit'])->name('admin.pages.edit');
    Route::get('/admin/pages/{page}/tree', [EditorController::class, 'tree'])->name('admin.pages.tree');
    Route::put('/admin/pages/{page}/tree', [EditorController::class, 'save'])->name('admin.pages.tree.save');
    Route::post('/admin/pages/{page}/preview', [EditorController::class, 'preview'])->name('admin.pages.preview');

    // Widget Builder (create custom widgets with no code)
    Route::get('/admin/widgets', [WidgetBuilderController::class, 'index'])->name('admin.widgets.index');
    Route::get('/admin/widgets/create', [WidgetBuilderController::class, 'create'])->name('admin.widgets.create');
    Route::post('/admin/widgets', [WidgetBuilderController::class, 'store'])->name('admin.widgets.store');
    Route::get('/admin/widgets/preview/{type}', [WidgetBuilderController::class, 'preview'])->name('admin.widgets.preview');
    Route::post('/admin/widgets/seed/{type}', [WidgetBuilderController::class, 'seed'])->name('admin.widgets.seed');
    Route::get('/admin/widgets/{id}/edit', [WidgetBuilderController::class, 'edit'])->name('admin.widgets.edit');
    Route::put('/admin/widgets/{id}', [WidgetBuilderController::class, 'update'])->name('admin.widgets.update');
    Route::delete('/admin/widgets/{id}', [WidgetBuilderController::class, 'destroy'])->name('admin.widgets.destroy');

    // Menus (navigation manager)
    Route::get('/admin/menus', [MenuController::class, 'index'])->name('admin.menus.index');
    Route::post('/admin/menus', [MenuController::class, 'store'])->name('admin.menus.store');
    Route::get('/admin/menus/{menu}', [MenuController::class, 'show'])->name('admin.menus.show');
    Route::put('/admin/menus/{menu}', [MenuController::class, 'update'])->name('admin.menus.update');
    Route::delete('/admin/menus/{menu}', [MenuController::class, 'destroy'])->name('admin.menus.destroy');
    Route::post('/admin/menus/{menu}/items', [MenuController::class, 'storeItem'])->name('admin.menus.items.store');
    Route::put('/admin/menus/{menu}/items/{item}', [MenuController::class, 'updateItem'])->name('admin.menus.items.update');
    Route::delete('/admin/menus/{menu}/items/{item}', [MenuController::class, 'destroyItem'])->name('admin.menus.items.destroy');

    // Admission leads (admission-type enquiries)
    Route::get('/admin/leads', [LeadController::class, 'index'])->name('admin.leads');
    Route::get('/admin/leads/export/csv', [LeadController::class, 'exportCsv'])->name('admin.leads.export.csv');
    Route::get('/admin/leads/export/pdf', [LeadController::class, 'exportPdf'])->name('admin.leads.export.pdf');

    // Admission Forms (builder + submissions)
    Route::get('/admin/forms', [FormController::class, 'index'])->name('admin.forms.index');
    Route::get('/admin/forms/create', [FormController::class, 'create'])->name('admin.forms.create');
    Route::post('/admin/forms', [FormController::class, 'store'])->name('admin.forms.store');
    Route::get('/admin/forms/{form}/edit', [FormController::class, 'edit'])->name('admin.forms.edit');
    Route::put('/admin/forms/{form}', [FormController::class, 'update'])->name('admin.forms.update');
    Route::delete('/admin/forms/{form}', [FormController::class, 'destroy'])->name('admin.forms.destroy');
    Route::get('/admin/forms/{form}/submissions', [FormController::class, 'submissions'])->name('admin.forms.submissions');

    // Upload Center
    Route::get('/admin/upload', [UploadController::class, 'index'])->name('admin.upload');
    Route::post('/admin/upload', [UploadController::class, 'store'])->name('admin.upload.store');
    Route::delete('/admin/upload/{id}', [UploadController::class, 'destroy'])->name('admin.upload.destroy');


    // Enquiries
    Route::get('/admin/enquiries', [InboxController::class, 'enquiries'])->name('admin.enquiries');
    Route::post('/admin/enquiries/{enquiry}/status', [InboxController::class, 'updateEnquiryStatus'])->name('admin.enquiries.status');

    // Job applications
    Route::get('/admin/applications', [InboxController::class, 'applications'])->name('admin.applications');
    Route::post('/admin/applications/{application}/status', [InboxController::class, 'updateApplicationStatus'])->name('admin.applications.status');
    Route::get('/admin/applications/{application}/resume', [InboxController::class, 'downloadResume'])->name('admin.applications.resume');

    // Subscribers
    Route::get('/admin/subscribers', [InboxController::class, 'subscribers'])->name('admin.subscribers');
    Route::post('/admin/subscribers/{subscriber}/unsubscribe', [InboxController::class, 'unsubscribe'])->name('admin.subscribers.unsubscribe');

    // Analytics
    Route::get('/admin/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics');

    // Global search (⌘K command palette)
    Route::get('/admin/search', [SearchController::class, 'search'])->name('admin.search');

    // System pages
    Route::get('/admin/settings', [SystemController::class, 'settings'])->name('admin.settings');
    Route::post('/admin/settings', [SystemController::class, 'saveSettings'])->name('admin.settings.save');

    // Theme Builder (visual header/footer pickers, colors, fonts)
    Route::get('/admin/theme', [ThemeController::class, 'index'])->name('admin.theme');
    Route::post('/admin/theme', [ThemeController::class, 'save'])->name('admin.theme.save');
    Route::post('/admin/theme/font', [ThemeController::class, 'uploadFont'])->name('admin.theme.font');
    Route::post('/admin/theme/font/remove', [ThemeController::class, 'removeFont'])->name('admin.theme.font.remove');
    Route::get('/admin/seo', [SystemController::class, 'seo'])->name('admin.seo');
    Route::get('/admin/seo/audit', [SystemController::class, 'audit'])->name('admin.seo.audit');
    Route::get('/admin/seo/bulk', [SystemController::class, 'seoBulk'])->name('admin.seo.bulk');
    Route::put('/admin/seo/bulk', [SystemController::class, 'seoBulkSave'])->name('admin.seo.bulk.save');
    Route::get('/admin/seo/{page}/edit', [SystemController::class, 'editSeo'])->name('admin.seo.edit');
    Route::put('/admin/seo/{page}', [SystemController::class, 'updateSeo'])->name('admin.seo.update');
    Route::get('/admin/404', [SystemController::class, 'notFound'])->name('admin.notfound');
    Route::post('/admin/404/{log}/redirect', [SystemController::class, 'notFoundRedirect'])->name('admin.notfound.redirect');
    Route::post('/admin/404/{log}/ignore', [SystemController::class, 'notFoundIgnore'])->name('admin.notfound.ignore');
    Route::get('/admin/system-health', [SystemController::class, 'health'])->name('admin.health');
    Route::get('/admin/notifications', [NotificationController::class, 'index'])->name('admin.notifications');
    Route::post('/admin/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('admin.notifications.read-all');
    Route::post('/admin/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('admin.notifications.read');

    // --- Phase 7 Dynamic Email System & Newsletter ---
    Route::prefix('/admin/settings/email-providers')->name('admin.email-providers.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\EmailProviderController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\EmailProviderController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\EmailProviderController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\EmailProviderController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\EmailProviderController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\EmailProviderController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/test', [\App\Http\Controllers\Admin\EmailProviderController::class, 'testConnection'])->name('test');
        Route::post('/{id}/set-active', [\App\Http\Controllers\Admin\EmailProviderController::class, 'setActive'])->name('set-active');
    });

    Route::prefix('/admin/settings/email-templates')->name('admin.email-templates.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'index'])->name('index');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'update'])->name('update');
        Route::post('/{id}/test-send', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'testSend'])->name('test-send');
        Route::post('/{id}/revert/{revisionId}', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'revert'])->name('revert');
        Route::post('/{id}/toggle', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'toggle'])->name('toggle');
    });

    Route::prefix('/admin/email-logs')->name('admin.email-logs.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\EmailLogController::class, 'index'])->name('index');
        Route::post('/{id}/resend', [\App\Http\Controllers\Admin\EmailLogController::class, 'resend'])->name('resend');
    });

    Route::prefix('/admin/newsletter/campaigns')->name('admin.newsletter.campaigns.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\NewsletterController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\NewsletterController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\NewsletterController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\NewsletterController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\NewsletterController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\NewsletterController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/send-now', [\App\Http\Controllers\Admin\NewsletterController::class, 'sendNow'])->name('send-now');
    });

    Route::prefix('/admin/newsletter/subscribers')->name('admin.newsletter.subscribers.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'index'])->name('index');
        Route::post('/{id}/unsubscribe', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'unsubscribe'])->name('unsubscribe');
        Route::get('/export', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'export'])->name('export');
    });

    // Instagram Feed (Facebook OAuth + Meta Graph API enterprise module)
    Route::prefix('/admin/instagram')->name('admin.instagram.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\Instagram\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/settings', [\App\Http\Controllers\Admin\Instagram\SettingsController::class, 'index'])->name('settings');
        Route::get('/oauth/connect', [\App\Http\Controllers\Admin\Instagram\OAuthController::class, 'redirect'])->name('oauth.connect');
        Route::get('/oauth/callback', [\App\Http\Controllers\Admin\Instagram\OAuthController::class, 'callback'])->name('oauth.callback');
        Route::post('/sync/{account}', [\App\Http\Controllers\Admin\Instagram\DashboardController::class, 'sync'])->name('sync');
        Route::post('/sync-all', [\App\Http\Controllers\Admin\Instagram\DashboardController::class, 'syncAll'])->name('sync.all');
        Route::post('/tokens/refresh', [\App\Http\Controllers\Admin\Instagram\DashboardController::class, 'refreshTokens'])->name('tokens.refresh');
        Route::post('/disconnect/{account}', [\App\Http\Controllers\Admin\Instagram\DashboardController::class, 'disconnect'])->name('disconnect');
    });

    // Media Library (dedicated view with grid/list/large modes + infinite scroll)
    Route::get('/admin/m/media', [MediaLibraryController::class, 'index'])->name('admin.media.index');
    Route::get('/admin/m/media/more', [MediaLibraryController::class, 'loadMore'])->name('admin.media.more');

    // Media Library API Endpoints for Modal & Picker
    Route::get('/admin/media-library/api', [MediaLibraryController::class, 'apiList']);
    Route::post('/admin/media-library/api/upload', [MediaLibraryController::class, 'apiUpload']);
    Route::put('/admin/media-library/api/{id}', [MediaLibraryController::class, 'apiUpdate']);
    Route::delete('/admin/media-library/api/{id}', [MediaLibraryController::class, 'apiDestroy']);
    Route::get('/admin/media-library/api/{id}/usage', [MediaLibraryController::class, 'apiCheckUsage']);
    Route::post('/admin/media-library/api/{id}/replace', [MediaLibraryController::class, 'apiReplace']);

    // Generic resource modules (config-driven CRUD via ResourceRegistry)
    Route::get('/admin/m/{resource}', [ResourceController::class, 'index'])->name('admin.resource.index');
    Route::get('/admin/m/{resource}/create', [ResourceController::class, 'create'])->name('admin.resource.create');
    Route::post('/admin/m/{resource}', [ResourceController::class, 'store'])->name('admin.resource.store');
    Route::get('/admin/m/{resource}/{id}/edit', [ResourceController::class, 'edit'])->name('admin.resource.edit');
    Route::put('/admin/m/{resource}/{id}', [ResourceController::class, 'update'])->name('admin.resource.update');
    Route::delete('/admin/m/{resource}/{id}', [ResourceController::class, 'destroy'])->name('admin.resource.destroy');

    // Role/User permission management
    Route::get('/admin/role-permissions', [\App\Http\Controllers\Admin\RolePermissionController::class, 'index'])->name('admin.role-permissions.index');
    Route::get('/admin/role-permissions/{role}/edit', [\App\Http\Controllers\Admin\RolePermissionController::class, 'edit'])->name('admin.role-permissions.edit');
    Route::put('/admin/role-permissions/{role}', [\App\Http\Controllers\Admin\RolePermissionController::class, 'update'])->name('admin.role-permissions.update');
    Route::get('/admin/user-roles', [\App\Http\Controllers\Admin\UserRoleController::class, 'index'])->name('admin.user-roles.index');
    Route::get('/admin/user-roles/{user}/edit', [\App\Http\Controllers\Admin\UserRoleController::class, 'edit'])->name('admin.user-roles.edit');
    Route::put('/admin/user-roles/{user}', [\App\Http\Controllers\Admin\UserRoleController::class, 'update'])->name('admin.user-roles.update');

    // --- Academic Calendar Admin CRUD ---
    Route::middleware('role:admin|principal')->prefix('admin')->name('admin.')->group(function () {
        Route::get('academic-calendar-entries/import', [\App\Http\Controllers\Admin\AcademicCalendarImportController::class, 'show'])->name('academic-calendar-entries.import');
        Route::get('academic-calendar-entries/import/sample', [\App\Http\Controllers\Admin\AcademicCalendarImportController::class, 'downloadSample'])->name('academic-calendar-entries.import.sample');
        Route::post('academic-calendar-entries/import/csv', [\App\Http\Controllers\Admin\AcademicCalendarImportController::class, 'importCsv'])->name('academic-calendar-entries.import.csv');
        Route::post('academic-calendar-entries/import/ai', [\App\Http\Controllers\Admin\AcademicCalendarImportController::class, 'importAi'])->name('academic-calendar-entries.import.ai');
        Route::post('academic-calendar-entries/import/save-review', [\App\Http\Controllers\Admin\AcademicCalendarImportController::class, 'saveReviewed'])->name('academic-calendar-entries.import.save-review');

        Route::resource('academic-calendar-entries', \App\Http\Controllers\Admin\AcademicCalendarEntryController::class);
        Route::post('academic-sessions/{academic_session}/toggle', [\App\Http\Controllers\Admin\AcademicSessionController::class, 'toggle'])->name('academic-sessions.toggle');
        Route::resource('academic-sessions', \App\Http\Controllers\Admin\AcademicSessionController::class);
        Route::resource('academic-terms', \App\Http\Controllers\Admin\AcademicTermController::class);

        // Mess Menu CRUD
        Route::get('mess-menus', [\App\Http\Controllers\Admin\MessMenuController::class, 'index'])->name('mess-menus.index');
        Route::get('mess-menus/create', [\App\Http\Controllers\Admin\MessMenuController::class, 'create'])->name('mess-menus.create');
        Route::post('mess-menus', [\App\Http\Controllers\Admin\MessMenuController::class, 'store'])->name('mess-menus.store');
        Route::get('mess-menus/{id}/edit', [\App\Http\Controllers\Admin\MessMenuController::class, 'edit'])->name('mess-menus.edit');
        Route::put('mess-menus/{id}', [\App\Http\Controllers\Admin\MessMenuController::class, 'update'])->name('mess-menus.update');
        Route::delete('mess-menus/{id}', [\App\Http\Controllers\Admin\MessMenuController::class, 'destroy'])->name('mess-menus.destroy');
        Route::post('mess-menus/{id}/toggle', [\App\Http\Controllers\Admin\MessMenuController::class, 'toggleActive'])->name('mess-menus.toggle');
        Route::post('mess-menus/{id}/duplicate', [\App\Http\Controllers\Admin\MessMenuController::class, 'duplicate'])->name('mess-menus.duplicate');
        Route::post('mess-menus/{id}/special', [\App\Http\Controllers\Admin\MessMenuController::class, 'storeSpecial'])->name('mess-menus.special.store');
        Route::delete('mess-menus/{id}/special/{specialId}', [\App\Http\Controllers\Admin\MessMenuController::class, 'destroySpecial'])->name('mess-menus.special.destroy');
    });

    // AI Chatbot Admin Console
    Route::prefix('/admin/chatbot')->name('admin.chatbot.')->group(function () {
        Route::get('/', [AdminChatbotController::class, 'index'])->name('index');
        Route::post('/settings', [AdminChatbotController::class, 'updateSettings'])->name('settings.update');
        Route::get('/conversations', [AdminChatbotController::class, 'conversations'])->name('conversations');
        Route::get('/conversations/list-json', [AdminChatbotController::class, 'listJson'])->name('conversations.list-json');
        Route::get('/conversations/{id}/messages', [AdminChatbotController::class, 'getMessages'])->name('conversations.messages');
        Route::post('/conversations/{id}/messages', [AdminChatbotController::class, 'sendMessage'])->name('conversations.send');
        Route::post('/conversations/{id}/status', [AdminChatbotController::class, 'updateConversationStatus'])->name('conversations.status');
        Route::post('/conversations/{id}/assign', [AdminChatbotController::class, 'assignConversation'])->name('conversations.assign');
        Route::get('/kb', [AdminChatbotController::class, 'kb'])->name('kb');
        Route::post('/kb/index-cms', [AdminChatbotController::class, 'indexCms'])->name('kb.index-cms');
        Route::post('/kb/upload', [AdminChatbotController::class, 'uploadDoc'])->name('kb.upload');
        Route::delete('/kb/{id}', [AdminChatbotController::class, 'deleteDoc'])->name('kb.delete');
        Route::get('/leads', [AdminChatbotController::class, 'leads'])->name('leads');
        Route::get('/flows', [AdminChatbotController::class, 'flows'])->name('flows');
        Route::post('/flows', [AdminChatbotController::class, 'saveFlow'])->name('flows.save');

        // Pre-Chat Form Builder
        Route::get('/form-fields', [AdminPreChatFormController::class, 'fields'])->name('form-fields');
        Route::post('/form-fields', [AdminPreChatFormController::class, 'storeField'])->name('form-fields.store');
        Route::put('/form-fields/{id}', [AdminPreChatFormController::class, 'updateField'])->name('form-fields.update');
        Route::delete('/form-fields/{id}', [AdminPreChatFormController::class, 'destroyField'])->name('form-fields.destroy');
        Route::post('/form-fields/{id}/toggle', [AdminPreChatFormController::class, 'toggleField'])->name('form-fields.toggle');
        Route::post('/form-fields/reorder', [AdminPreChatFormController::class, 'reorderFields'])->name('form-fields.reorder');
        Route::get('/form-fields/submissions', [AdminPreChatFormController::class, 'submissions'])->name('form-fields.submissions');

        // Canned Responses
        Route::get('/canned',            [AdminChatbotController::class, 'cannedResponses'])->name('canned');
        Route::post('/canned',           [AdminChatbotController::class, 'storeCanned'])->name('canned.store');
        Route::put('/canned/{id}',       [AdminChatbotController::class, 'updateCanned'])->name('canned.update');
        Route::delete('/canned/{id}',    [AdminChatbotController::class, 'destroyCanned'])->name('canned.destroy');
        Route::get('/canned/suggest',    [AdminChatbotController::class, 'suggestCanned'])->name('canned.suggest');

        // Conversational Assistants
        Route::get('/assistant', [AdminChatbotController::class, 'assistantConfig'])->name('assistant');
        Route::post('/assistant', [AdminChatbotController::class, 'saveAssistantConfig'])->name('assistant.save');

        // Campaigns
        Route::resource('campaigns', \App\Http\Controllers\Admin\CampaignController::class)->except(['show']);
        Route::post('/campaigns/{id}/send', [\App\Http\Controllers\Admin\CampaignController::class, 'send'])->name('campaigns.send');
        Route::post('/campaigns/{id}/duplicate', [\App\Http\Controllers\Admin\CampaignController::class, 'duplicate'])->name('campaigns.duplicate');

        // Webhooks
        Route::resource('webhooks', \App\Http\Controllers\Admin\WebhookController::class);
        Route::post('/webhooks/{id}/test', [\App\Http\Controllers\Admin\WebhookController::class, 'test'])->name('webhooks.test');

        // Webhook Logs
        Route::get('/webhook-logs', [\App\Http\Controllers\Admin\WebhookController::class, 'logs'])->name('webhook-logs');
        Route::get('/webhook-logs/{id}', [\App\Http\Controllers\Admin\WebhookController::class, 'showLog'])->name('webhook-logs.show');

        // Analytics (already defined via enterprise module but add explicit route)
        Route::get('/analytics', [\App\Core\Chatbot\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics');
        Route::get('/analytics/reports', [\App\Core\Chatbot\Http\Controllers\AnalyticsController::class, 'reports'])->name('analytics.reports');
        Route::post('/analytics/generate-report', [\App\Core\Chatbot\Http\Controllers\AnalyticsController::class, 'generateReport'])->name('analytics.generate-report');
        Route::get('/analytics/realtime', [\App\Core\Chatbot\Http\Controllers\AnalyticsController::class, 'realtime'])->name('analytics.realtime');
    });

    // 2FA
    Route::prefix('2fa')->middleware('auth')->name('2fa.')->group(function () {
        Route::get('/setup', [\App\Http\Controllers\Auth\TwoFactorController::class, 'showSetup'])->name('setup');
        Route::post('/enable', [\App\Http\Controllers\Auth\TwoFactorController::class, 'enable'])->name('enable');
        Route::post('/disable', [\App\Http\Controllers\Auth\TwoFactorController::class, 'disable'])->name('disable');
    });

    // API Tokens
    Route::prefix('/admin/api-tokens')->name('admin.api-tokens.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ApiTokenController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\ApiTokenController::class, 'store'])->name('store');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\ApiTokenController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/revoke', [\App\Http\Controllers\Admin\ApiTokenController::class, 'revoke'])->name('revoke');
    });

    // CMS Update System
    Route::prefix('/admin/updates')->name('admin.updates.')->group(function () {
        Route::get('/',               [\App\Http\Controllers\Admin\UpdateController::class, 'index'])->name('index');
        Route::post('/upload',        [\App\Http\Controllers\Admin\UpdateController::class, 'upload'])->name('upload');
        Route::get('/confirm',        [\App\Http\Controllers\Admin\UpdateController::class, 'confirm'])->name('confirm');
        Route::post('/apply',         [\App\Http\Controllers\Admin\UpdateController::class, 'apply'])->name('apply');
        Route::post('/backup',        [\App\Http\Controllers\Admin\UpdateController::class, 'backup'])->name('backup');
        Route::post('/git-pull',      [\App\Http\Controllers\Admin\UpdateController::class, 'gitPull'])->name('git-pull');
        Route::post('/{id}/rollback', [\App\Http\Controllers\Admin\UpdateController::class, 'rollback'])->name('rollback');
    });



    // Funnel Analytics
    Route::get('/admin/funnel', [\App\Http\Controllers\Admin\FunnelController::class, 'index'])->name('admin.funnel');
    Route::get('/admin/funnel/data', [\App\Http\Controllers\Admin\FunnelController::class, 'data'])->name('admin.funnel.data');

    // Parent Testimonials Management Console
    Route::prefix('/admin/testimonials-console')->name('admin.testimonials-console.')->group(function () {
        Route::get('/', [AdminTestimonialController::class, 'index'])->name('index');
        Route::get('/view/{id}', [AdminTestimonialController::class, 'view'])->name('view');
        Route::post('/approve/{id}', [AdminTestimonialController::class, 'approve'])->name('approve');
        Route::post('/reject/{id}', [AdminTestimonialController::class, 'reject'])->name('reject');
        Route::post('/toggle-featured/{id}', [AdminTestimonialController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::post('/toggle-verified/{id}', [AdminTestimonialController::class, 'toggleVerified'])->name('toggle-verified');
        Route::get('/export', [AdminTestimonialController::class, 'export'])->name('export');
        Route::post('/import', [AdminTestimonialController::class, 'import'])->name('import');
        Route::post('/bulk', [AdminTestimonialController::class, 'bulkAction'])->name('bulk');
        Route::get('/settings', [AdminTestimonialController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminTestimonialController::class, 'updateSettings'])->name('settings.update');
        Route::get('/edit/{id}', [AdminTestimonialController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [AdminTestimonialController::class, 'update'])->name('update');
        Route::post('/duplicate/{id}', [AdminTestimonialController::class, 'duplicate'])->name('duplicate');
        Route::delete('/delete/{id}', [AdminTestimonialController::class, 'destroy'])->name('destroy');
    });

    // Video Testimonials Admin Console
    Route::prefix('/admin/video-testimonials')->name('admin.video-testimonials.')->group(function () {
        Route::get('/', [VideoTestimonialController::class, 'index'])->name('index');
        Route::get('/create', [VideoTestimonialController::class, 'create'])->name('create');
        Route::post('/', [VideoTestimonialController::class, 'store'])->name('store');
        Route::get('/settings', [\App\Http\Controllers\Admin\VideoTestimonialSettingsController::class, 'index'])->name('settings');
        Route::post('/settings', [\App\Http\Controllers\Admin\VideoTestimonialSettingsController::class, 'update'])->name('settings.update');
        Route::post('/settings/sync-instagram', [\App\Http\Controllers\Admin\VideoTestimonialSettingsController::class, 'syncInstagram'])->name('settings.sync-instagram');
        Route::get('/analytics', [VideoTestimonialAnalyticsController::class, 'index'])->name('analytics');
        Route::get('/{id}/edit', [VideoTestimonialController::class, 'edit'])->name('edit');
        Route::patch('/{id}', [VideoTestimonialController::class, 'update'])->name('update');
        Route::post('/{id}/approve', [VideoTestimonialController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [VideoTestimonialController::class, 'reject'])->name('reject');
        Route::delete('/{id}', [VideoTestimonialController::class, 'destroy'])->name('destroy');
    });

    // GDPR & Data Privacy (Admin)
    Route::prefix('/admin/privacy')->name('admin.privacy.')->group(function () {
        Route::get('/', [\App\Core\Privacy\Http\Controllers\Admin\PrivacyDashboardController::class, 'index'])->name('index');
        Route::post('/{id}/approve', [\App\Core\Privacy\Http\Controllers\Admin\PrivacyDashboardController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [\App\Core\Privacy\Http\Controllers\Admin\PrivacyDashboardController::class, 'reject'])->name('reject');
        Route::get('/{id}/download', [\App\Core\Privacy\Http\Controllers\Admin\PrivacyDashboardController::class, 'download'])->name('download');
    });

    // Security Audit Trail (Admin)
    Route::prefix('/admin/audit')->name('admin.audit.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AuditTrailController::class, 'index'])->name('index');
        Route::get('/export', [\App\Http\Controllers\Admin\AuditTrailController::class, 'exportCsv'])->name('export');
        Route::get('/{id}', [\App\Http\Controllers\Admin\AuditTrailController::class, 'show'])->name('show');
    });
});

// Embeddable chatbot JS (cross-site, served with CORS)
Route::get('/chatbot/embed.js', [\App\Http\Controllers\Cms\ChatbotEmbedController::class, 'embedJs'])->name('chatbot.embed.js');

// AI Chatbot Widget APIs (with CORS for cross-site embedding)
Route::prefix('/chatbot/widget')->middleware('cors')->name('chatbot.widget.')->group(function () {
    Route::get('/config', [PublicChatbotController::class, 'config'])->name('config');
    Route::get('/form-fields', [PublicChatbotController::class, 'formFields'])->name('form-fields');
    Route::post('/init', [PublicChatbotController::class, 'init'])->name('init');
    Route::post('/send', [PublicChatbotController::class, 'send'])->name('send');
    Route::post('/lead', [PublicChatbotController::class, 'submitLead'])->name('lead');
    Route::get('/conversations/{id}/messages', [PublicChatbotController::class, 'getMessages'])->name('messages');
    Route::post('/conversations/{id}/close', [PublicChatbotController::class, 'closeConversation'])->name('close');
    Route::post('/upload', [PublicChatbotController::class, 'uploadFile'])->name('upload');
    Route::post('/typing', [PublicChatbotController::class, 'typing'])->name('typing');
    Route::post('/read', [PublicChatbotController::class, 'markRead'])->name('read');
});

// Visitor Tracking API (public, used by widget JS)
Route::prefix('/chatbot/track')->middleware('cors')->name('chatbot.track.')->group(function () {
    Route::post('/identify', [\App\Http\Controllers\Cms\VisitorTrackController::class, 'identify'])->name('identify');
    Route::post('/page', [\App\Http\Controllers\Cms\VisitorTrackController::class, 'pageView'])->name('page');
    Route::post('/event', [\App\Http\Controllers\Cms\VisitorTrackController::class, 'event'])->name('event');
    Route::post('/heartbeat', [\App\Http\Controllers\Cms\VisitorTrackController::class, 'heartbeat'])->name('heartbeat');
    Route::post('/end', [\App\Http\Controllers\Cms\VisitorTrackController::class, 'endSession'])->name('end');
});

// --- Academic Calendar Public Routes ---
Route::get('/academic-calendar', [\App\Http\Controllers\AcademicCalendarController::class, 'index'])
    ->middleware('http.cache:300')
    ->name('academic-calendar.index');
Route::get('/academic-calendar/feed', [\App\Http\Controllers\AcademicCalendarController::class, 'feed'])
    ->middleware('http.cache:300')
    ->name('academic-calendar.feed');
Route::get('/academic-calendar/export-pdf', [\App\Http\Controllers\AcademicCalendarController::class, 'exportPdf'])
    ->middleware('http.cache:600')
    ->name('academic-calendar.pdf');

// --- Mess Menu (public page + PDF) ---
Route::get('/mess-menu', [\App\Http\Controllers\MessMenuController::class, 'index'])
    ->middleware('http.cache:300')
    ->name('mess-menu.index');
Route::get('/mess-menu/pdf', [\App\Http\Controllers\MessMenuController::class, 'downloadPdf'])
    ->middleware(['throttle:10,1', 'http.cache:600'])
    ->name('mess-menu.pdf');

// --- Instagram Feed (public API for load-more) ---
Route::get('/__ig/feed', [\App\Http\Controllers\Cms\InstagramFeedController::class, 'feed'])->middleware('http.cache:300');

// --- Public form submissions (rate-limited, honeypot-guarded) ---
Route::post('/enquiries', [EnquiryController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('enquiries.store');

Route::post('/jobs/apply', [JobApplicationController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('jobs.apply');

Route::post('/subscribe', [SubscriberController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('subscribe.store');

// --- Standalone Online Registration Form ---
Route::get('/registration', [\App\Http\Controllers\Cms\RegistrationController::class, 'show'])->name('admissions.register');
Route::get('/admissions/register', [\App\Http\Controllers\Cms\RegistrationController::class, 'show']);
Route::post('/admissions/store', [\App\Http\Controllers\Cms\RegistrationController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('admissions.store');

// --- Public custom forms (admission enquiry forms, etc.) ---
Route::get('/forms/{slug}', [PublicFormController::class, 'show'])->middleware('http.cache:300')->name('public.form');
Route::post('/forms/{slug}', [PublicFormController::class, 'submit'])
    ->middleware('throttle:10,1')
    ->name('public.form.submit');

// --- SEO (generated live from published pages) ---
Route::get('/sitemap.xml', [SitemapController::class, 'sitemap'])->middleware('http.cache:600')->name('sitemap');
Route::get('/sitemap-pages.xml', [SitemapController::class, 'pages'])->middleware('http.cache:600')->name('sitemap.pages');
Route::get('/sitemap-images.xml', [SitemapController::class, 'images'])->middleware('http.cache:600')->name('sitemap.images');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->middleware('http.cache:86400')->name('robots');
// IndexNow ownership key file (only matches lowercase-hex .txt filenames).
Route::get('/{key}.txt', [SitemapController::class, 'indexNowKey'])->where('key', '[a-f0-9]{8,128}')->name('indexnow.key');

// --- Public marketing site ---
// The home page is now fully builder-driven (CMS). The original static design
// is preserved at /legacy-home for reference/rollback.
Route::get('/', [PageController::class, 'home'])->middleware('http.cache:300')->name('home');
Route::get('/legacy-home', [SiteController::class, 'home'])->name('legacy.home');
Route::get('/search', [SiteSearchController::class, 'index'])->name('search');

// --- Public custom testimonials ---
Route::post('/testimonials', [PublicTestimonialController::class, 'store'])->middleware('throttle:10,1')->name('testimonials.store');
Route::get('/testimonials', [PublicTestimonialController::class, 'index'])->middleware('http.cache:300')->name('testimonials.index');
Route::get('/api/testimonials', [PublicTestimonialController::class, 'apiList'])->middleware('http.cache:300')->name('testimonials.api.list');
Route::get('/api/featured-testimonials', [PublicTestimonialController::class, 'apiFeatured'])->middleware('http.cache:300')->name('testimonials.api.featured');

// --- Video Testimonials (public submission) ---
Route::get('/video-testimonials/submit', [VideoTestimonialSubmissionController::class, 'show'])
    ->middleware('http.cache:300')
    ->name('video-testimonials.submit');
Route::post('/video-testimonials/submit', [VideoTestimonialSubmissionController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('video-testimonials.submit.store');
Route::post('/video-testimonials/track', [VideoTestimonialAnalyticsController::class, 'track'])
    ->middleware('throttle:60,1')
    ->name('video-testimonials.track');

// --- GDPR / Data Privacy Public Requests ---
Route::get('/privacy/request-my-data', [\App\Core\Privacy\Http\Controllers\PrivacyRequestController::class, 'showForm'])->name('privacy.form');
Route::post('/privacy/request-my-data', [\App\Core\Privacy\Http\Controllers\PrivacyRequestController::class, 'submit'])->name('privacy.submit');
Route::get('/privacy/verify/{token}', [\App\Core\Privacy\Http\Controllers\PrivacyRequestController::class, 'verify'])->name('privacy.verify');

// --- Public Newsletter Double Opt-in & Unsubscribe ---
Route::get('/newsletter/confirm/{token}', [\App\Http\Controllers\Cms\NewsletterController::class, 'confirm'])->name('newsletter.confirm');
Route::get('/newsletter/unsubscribe/{id}', [\App\Http\Controllers\Cms\NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// --- Public CMS (kept last; catch-all excludes reserved paths) ---
Route::get('/cms-home', [PageController::class, 'home'])->name('cms.home'); // alias of /
Route::get('/{slug}', [PageController::class, 'show'])
    ->middleware('http.cache:300')
    ->where('slug', '(?!up$|login$|logout$|admin$|enquiries$|jobs$|subscribe$|search$|registration$|admissions/store$|legacy-home$|cms-home$|testimonials$|video-testimonials$|api$|privacy$|privacy/)[A-Za-z0-9\-_/]+');
