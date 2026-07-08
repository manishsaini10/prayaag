<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Instagram;

use App\Http\Controllers\Controller;
use App\Models\InstagramSyncLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $env = [
            'app_id'          => config('instagram.app_id') ? '••••••' . substr(config('instagram.app_id'), -4) : '',
            'app_secret'      => config('instagram.app_secret') ? '••••••' . substr(config('instagram.app_secret'), -4) : '',
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
}
