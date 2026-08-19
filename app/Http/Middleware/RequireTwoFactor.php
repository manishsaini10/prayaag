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

            // Skip checks for 2FA actions or logging out to avoid redirect loops
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

            // If the user has 2FA enabled and has NOT passed the session challenge,
            // redirect them to the challenge page to enter their TOTP code.
            if ($user->two_factor_enabled && !session('2fa_passed')) {
                return redirect()->route('2fa.challenge');
            }

            // NOTE: Mandatory 2FA setup enforcement has been removed.
            // Admin users who have NOT enabled 2FA can continue using the panel normally.
            // They can optionally set it up via their profile settings.
        }

        return $next($request);
    }
}
