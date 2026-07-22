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
        // SEO 404 monitor: record missing URLs, then fall through to the
        // default 404 response (returning null keeps Laravel's rendering).
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            rescue(fn () => app(\App\Core\Seo\NotFoundLogger::class)->log($request), null, false);

            return null;
        });
    })->create();
