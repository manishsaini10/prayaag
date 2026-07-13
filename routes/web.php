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
use App\Http\Controllers\Cms\PublicChatbotController;
use App\Http\Controllers\Admin\AdminTestimonialController;
use App\Http\Controllers\Cms\PublicTestimonialController;
use Illuminate\Support\Facades\Route;

// --- Authentication ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- Admin (authenticated) ---
Route::middleware('auth')->group(function () {
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');

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
    });

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
});

// AI Chatbot Widget APIs
Route::prefix('/chatbot/widget')->name('chatbot.widget.')->group(function () {
    Route::get('/config', [PublicChatbotController::class, 'config'])->name('config');
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
Route::prefix('/chatbot/track')->name('chatbot.track.')->group(function () {
    Route::post('/identify', [\App\Http\Controllers\Cms\VisitorTrackController::class, 'identify'])->name('identify');
    Route::post('/page', [\App\Http\Controllers\Cms\VisitorTrackController::class, 'pageView'])->name('page');
    Route::post('/event', [\App\Http\Controllers\Cms\VisitorTrackController::class, 'event'])->name('event');
    Route::post('/heartbeat', [\App\Http\Controllers\Cms\VisitorTrackController::class, 'heartbeat'])->name('heartbeat');
    Route::post('/end', [\App\Http\Controllers\Cms\VisitorTrackController::class, 'endSession'])->name('end');
});

// --- Academic Calendar Public Routes ---
Route::get('/academic-calendar', [\App\Http\Controllers\AcademicCalendarController::class, 'index'])->name('academic-calendar.index');
Route::get('/academic-calendar/feed', [\App\Http\Controllers\AcademicCalendarController::class, 'feed'])->name('academic-calendar.feed');
Route::get('/academic-calendar/export-pdf', [\App\Http\Controllers\AcademicCalendarController::class, 'exportPdf'])->name('academic-calendar.pdf');

// --- Instagram Feed (public API for load-more) ---
Route::get('/__ig/feed', [\App\Http\Controllers\Cms\InstagramFeedController::class, 'feed']);

// --- Public form submissions (rate-limited, honeypot-guarded) ---
Route::post('/enquiries', [EnquiryController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('enquiries.store');

Route::post('/jobs/apply', [JobApplicationController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('jobs.apply');

Route::post('/subscribe', [SubscriberController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('subscribe.store');

// --- Public custom forms (admission enquiry forms, etc.) ---
Route::get('/forms/{slug}', [PublicFormController::class, 'show'])->name('public.form');
Route::post('/forms/{slug}', [PublicFormController::class, 'submit'])
    ->middleware('throttle:10,1')
    ->name('public.form.submit');

// --- SEO (generated live from published pages) ---
Route::get('/sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');
Route::get('/sitemap-pages.xml', [SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('/sitemap-images.xml', [SitemapController::class, 'images'])->name('sitemap.images');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');
// IndexNow ownership key file (only matches lowercase-hex .txt filenames).
Route::get('/{key}.txt', [SitemapController::class, 'indexNowKey'])->where('key', '[a-f0-9]{8,128}')->name('indexnow.key');

// --- Public marketing site ---
// The home page is now fully builder-driven (CMS). The original static design
// is preserved at /legacy-home for reference/rollback.
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/legacy-home', [SiteController::class, 'home'])->name('legacy.home');
Route::get('/search', [SiteSearchController::class, 'index'])->name('search');

// --- Public custom testimonials ---
Route::post('/testimonials', [PublicTestimonialController::class, 'store'])->middleware('throttle:10,1')->name('testimonials.store');
Route::get('/testimonials', [PublicTestimonialController::class, 'index'])->name('testimonials.index');
Route::get('/api/testimonials', [PublicTestimonialController::class, 'apiList'])->name('testimonials.api.list');
Route::get('/api/featured-testimonials', [PublicTestimonialController::class, 'apiFeatured'])->name('testimonials.api.featured');

// --- Public CMS (kept last; catch-all excludes reserved paths) ---
Route::get('/cms-home', [PageController::class, 'home'])->name('cms.home'); // alias of /
Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '(?!up$|login$|logout$|admin$|enquiries$|jobs$|subscribe$|search$|legacy-home$|cms-home$|testimonials$|api$)[A-Za-z0-9\-_/]+');
