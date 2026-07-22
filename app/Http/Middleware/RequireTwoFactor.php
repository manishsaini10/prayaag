<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequireTwoFactor
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user) {
            $route = $request->route();
            $routeName = $route ? $route->getName() : '';

            // 1. Skip checks for 2FA actions or logging out to avoid redirect loops
            $excluded = [
                '2fa.challenge',
                '2fa.verify',
                '2fa.setup',
                '2fa.enable',
                '2fa.disable',
                'logout',
            ];

            if (in_array($routeName, $excluded)) {
                return $next($request);
            }

            // 2. Enforce challenge validation if 2FA is enabled
            if ($user->two_factor_enabled && !session('2fa_passed')) {
                return redirect()->route('2fa.challenge');
            }

            // 3. Enforce mandatory 2FA setup for Admin/Super-Admin roles
            $hasAdminRole = $user->roles->contains(fn ($role) => in_array($role->name, ['admin', 'super-admin']));
            if ($hasAdminRole && !$user->two_factor_enabled) {
                return redirect()->route('2fa.setup')
                    ->with('warning', 'Two-Factor Authentication is mandatory for your administrative role. Please set up 2FA to proceed.');
            }
        }

        return $next($request);
    }
}
