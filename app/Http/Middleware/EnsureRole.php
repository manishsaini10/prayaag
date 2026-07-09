<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // If user is super-admin, allow anything.
        if ($request->user()->hasRole('super-admin')) {
            return $next($request);
        }

        foreach ($roles as $role) {
            // Support check for multiple roles formatted as "admin|principal"
            $subRoles = explode('|', $role);
            foreach ($subRoles as $subRole) {
                if ($request->user()->hasRole($subRole)) {
                    return $next($request);
                }
            }
        }

        abort(403, 'Unauthorized action. Required roles: ' . implode(', ', $roles));
    }
}
