<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Instagram;

use App\Http\Controllers\Controller;
use App\Services\Instagram\InstagramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OAuthController extends Controller
{
    public function __construct(
        private readonly InstagramService $instagram,
    ) {}

    public function redirect(): RedirectResponse
    {
        if (empty(config('instagram.app_id')) || empty(config('instagram.app_secret'))) {
            return redirect()->route('admin.instagram.settings')
                ->withErrors([
                    'oauth' => 'Facebook App is not configured. Open Settings > Environment Configuration to see what\'s needed, then set FACEBOOK_APP_ID and FACEBOOK_APP_SECRET in your .env file and run: php artisan config:clear'
                ]);
        }

        return redirect()->away($this->instagram->oauth->authorizationUrl());
    }

    public function callback(Request $request): RedirectResponse
    {
        $code = $request->query('code');
        $state = $request->query('state');

        if (! $code) {
            $error = $request->query('error_message', 'Authorization was cancelled or denied.');
            return redirect()->route('admin.instagram.dashboard')
                ->withErrors(['oauth' => $error]);
        }

        try {
            $account = $this->instagram->connect($code, $state);

            // Dispatch initial sync
            if (config('instagram.enable_queue')) {
                \App\Jobs\SyncInstagramMedia::dispatch($account->id);
            } else {
                $this->instagram->sync($account->id);
            }

            return redirect()->route('admin.instagram.dashboard')
                ->with('status', "Instagram account @{$account->username} connected successfully! Feed is syncing.");
        } catch (\Throwable $e) {
            return redirect()->route('admin.instagram.dashboard')
                ->withErrors(['oauth' => $e->getMessage()]);
        }
    }
}
