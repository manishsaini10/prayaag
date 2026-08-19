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

use App\Http\Controllers\Chatbot\SentimentController;
use App\Http\Controllers\Api\V1\PublicApiController;

Route::post('chatbot/sentiment', [SentimentController::class, 'analyse']);

// ── Versioned REST API v1 (Headless & Mobile) ──────────────────────────────
Route::prefix('v1')->middleware(['cors', 'http.cache:300'])->name('api.v1.')->group(function () {
    Route::get('pages/{slug}', [PublicApiController::class, 'page'])->name('page');
    Route::get('mess-menu', [PublicApiController::class, 'messMenu'])->name('mess-menu');
    Route::get('academic-calendar', [PublicApiController::class, 'academicCalendar'])->name('academic-calendar');
    Route::get('testimonials', [PublicApiController::class, 'testimonials'])->name('testimonials');
    Route::get('video-testimonials', [PublicApiController::class, 'videoTestimonials'])->name('video-testimonials');
    Route::get('jobs', [PublicApiController::class, 'jobListings'])->name('jobs');
});

// ── Automated Git Deployment Webhook ─────────────────────────────────────────
Route::match(['GET', 'POST'], 'deploy/webhook', function (\Illuminate\Http\Request $request, \App\Core\Updater\AutoDeployerService $deployer) {
    $expectedToken = substr(hash('sha256', config('app.key') . 'deploy_secret'), 0, 32);
    $providedToken = $request->input('token') ?? $request->header('X-Deploy-Token');

    if (!$providedToken || !hash_equals($expectedToken, $providedToken)) {
        return response()->json(['error' => 'Unauthorized: Invalid token'], 403);
    }

    $branch = $request->input('branch', 'main');
    $result = $deployer->backupAndDeploy($branch);

    return response()->json($result, $result['success'] ? 200 : 500);
});

// ── Deployment Health Check API ──────────────────────────────────────────────
Route::get('deploy/health', function (\App\Core\Updater\DeploymentHealthChecker $checker) {
    $result = $checker->runFullHealthCheck(maxRetries: 1, timeoutSeconds: 6);
    return response()->json([
        'status'      => $result['status'] === 'healthy' ? 'ok' : 'unhealthy',
        'application' => $result['checks']['backend'] ?? 'unknown',
        'database'    => $result['checks']['database'] ?? 'unknown',
        'cache'       => $result['checks']['cache'] ?? 'unknown',
        'storage'     => $result['checks']['storage'] ?? 'unknown',
        'assets'      => $result['checks']['assets'] ?? 'unknown',
        'frontend'    => $result['checks']['frontend'] ?? 'unknown',
        'checked_at'  => $result['checked_at'] ?? now()->toIso8601String(),
    ], $result['status'] === 'healthy' ? 200 : 503);
});



