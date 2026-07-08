<?php

namespace App\Core\Auth\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route guard: ->middleware('permission:pages.create')
 * Aborts 403 unless the authenticated user passes the gate.
 */
class EnsurePermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (! $request->user() || ! $request->user()->can($permission)) {
            abort(403);
        }

        return $next($request);
    }
}
