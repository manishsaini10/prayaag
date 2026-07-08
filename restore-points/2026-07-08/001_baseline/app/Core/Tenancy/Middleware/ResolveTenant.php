<?php

namespace App\Core\Tenancy\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * NO-OP (multi-tenancy removed — single-site CMS).
 *
 * No longer registered in bootstrap/app.php and performs no host resolution.
 * Retained only to avoid a dangling class reference during the conversion;
 * safe to delete.
 */
class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
