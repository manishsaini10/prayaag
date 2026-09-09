<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Instagram;

use App\Http\Controllers\Controller;
use App\Models\InstagramSyncLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /** Helper: get a setting from DB or fall back to config/env */
    private function getSetting(string $key, mixed $default = ''): string
    {
        $row = DB::table('instagram_app_settings')->where('key', $key)->first();
        return $row ? ($row->value ?? '') : (string) $default;
    }

    /** Helper: upsert a setting in DB */
    private function saveSetting(string $key, string $value): void
    {
        DB::table('instagram_app_settings')->updateOrInsert(
            ['key' => $key],
            ['value' => $value, 'updated_at' => now(), 'created_at' => now()]
        );
    }

    public function index(): View
    {
        // Read App ID/Secret from DB first, then fall back to env
        $dbAppId     = $this->getSetting('facebook_app_id', config('instagram.app_id', ''));
        $dbAppSecret = $this->getSetting('facebook_app_secret', config('instagram.app_secret', ''));

        $env = [
            'app_id'          => $dbAppId ? '••••••' . substr($dbAppId, -4) : '',
            'app_secret'      => $dbAppSecret ? '••••••' . substr($dbAppSecret, -4) : '',
            'app_id_set'      => !empty($dbAppId),
            'app_secret_set'  => !empty($dbAppSecret),
            'redirect_uri'    => url(config('instagram.redirect_uri')),
            'graph_version'   => config('instagram.graph_version'),
            'cache_duration'  => config('instagram.cache_duration'),
            'sync_interval'   => config('instagram.sync_interval'),
            'enable_queue'    => config('instagram.enable_queue'),
            'enable_cache'    => config('instagram.enable_local_cache'),
            'enable_webp'     => config('instagram.enable_webp'),
        ];

        $logs = InstagramSyncLog::latest()->limit(50)->get();

        return view('admin.instagram.settings', compact('env', 'logs'));
    }

    /** Save App ID and App Secret from admin form → DB */
    public function saveAppCredentials(Request $request): RedirectResponse
    {
        $request->validate([
            'facebook_app_id'     => 'required|string|min:5|max:30',
            'facebook_app_secret' => 'required|string|min:10|max:100',
        ], [
            'facebook_app_id.required'     => 'The Meta App ID is required.',
            'facebook_app_secret.required' => 'The Meta App Secret is required.',
        ]);

        $this->saveSetting('facebook_app_id',     trim($request->facebook_app_id));
        $this->saveSetting('facebook_app_secret',  trim($request->facebook_app_secret));

        // Dynamically update config so current request also uses new values
        config([
            'instagram.app_id'     => trim($request->facebook_app_id),
            'instagram.app_secret' => trim($request->facebook_app_secret),
        ]);

        return redirect()->route('admin.instagram.settings')
            ->with('status', '✅ App ID and App Secret saved successfully! Now click "Connect Instagram" to authenticate.');
    }
}
