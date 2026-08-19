<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Multi-tenancy removed: single-site CMS, no tenant-resolution middleware.
        $middleware->alias([
            'permission'  => \App\Core\Auth\Middleware\EnsurePermission::class,
            'role'        => \App\Http\Middleware\EnsureRole::class,
            'cors'        => \App\Http\Middleware\CorsMiddleware::class,
            'require.2fa' => \App\Http\Middleware\RequireTwoFactor::class,
            'http.cache'  => \App\Http\Middleware\HttpCacheMiddleware::class,
        ]);

        // SEO: perform configured 301/302 redirects on public GET requests.
        $middleware->web(append: [
            \App\Http\Middleware\HandleRedirects::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'chatbot/widget/*',
            'testimonials',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Report exceptions through SentryReporter (sanitizes sensitive data)
        $exceptions->report(function (Throwable $e) {
            rescue(fn () => app(\App\Services\SentryReporter::class)->capture($e), null, false);
        });

        // SEO 404 monitor: record missing URLs
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            rescue(fn () => app(\App\Core\Seo\NotFoundLogger::class)->log($request), null, false);

            if ($request->is('api/*') || $request->is('chatbot/*') || $request->is('__ig/*') || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error'   => [
                        'code'    => 'NOT_FOUND',
                        'message' => 'The requested resource or endpoint was not found.',
                    ],
                ], 404);
            }

            return null;
        });

        // Standardized JSON error response envelope for API requests on internal errors
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->is('chatbot/*') || $request->is('__ig/*') || $request->wantsJson()) {
                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                $message = config('app.debug') ? $e->getMessage() : 'An unexpected server error occurred.';

                return response()->json([
                    'success' => false,
                    'error'   => [
                        'code'    => 'SERVER_ERROR',
                        'message' => $message,
                    ],
                ], $status);
            }

            return null;
        });
    })->create();
