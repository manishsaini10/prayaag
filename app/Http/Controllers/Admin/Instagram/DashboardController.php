<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Instagram;

use App\Http\Controllers\Controller;
use App\Jobs\SyncInstagramMedia;
use App\Services\Instagram\InstagramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly InstagramService $instagram,
    ) {}

    public function index(): View
    {
        $stats = $this->instagram->getDashboardStats();

        return view('admin.instagram.dashboard', [
            'stats'             => $stats,
            'accounts'          => $stats['connected_accounts'],
            'totalAccounts'     => $stats['total_accounts'],
            'totalFollowers'    => $stats['total_followers'],
            'totalMedia'        => $stats['total_media'],
            'latestSync'        => $stats['latest_sync'],
            'expiringTokens'    => $stats['expiring_tokens'],
            'recentLogs'        => $stats['recent_logs'],
        ]);
    }

    public function sync(string $id): RedirectResponse
    {
        if (config('instagram.enable_queue')) {
            SyncInstagramMedia::dispatch($id);
            $message = 'Sync dispatched to queue.';
        } else {
            $result = $this->instagram->sync($id);
            $message = $result->message;
        }

        return redirect()->route('admin.instagram.dashboard')
            ->with('status', $message);
    }

    public function syncAll(): RedirectResponse
    {
        if (config('instagram.enable_queue')) {
            foreach (\App\Models\InstagramAccount::connected()->pluck('id') as $id) {
                SyncInstagramMedia::dispatch($id);
            }
            $message = 'Sync dispatched for all accounts.';
        } else {
            $results = $this->instagram->syncAll();
            $message = collect($results)->pluck('message')->implode(' | ');
        }

        return redirect()->route('admin.instagram.dashboard')
            ->with('status', $message);
    }

    public function refreshTokens(): RedirectResponse
    {
        $results = $this->instagram->refreshTokens();

        return redirect()->route('admin.instagram.dashboard')
            ->with('status', 'Token refresh completed.');
    }

    public function disconnect(string $id): RedirectResponse
    {
        try {
            $this->instagram->disconnect($id);
            return redirect()->route('admin.instagram.dashboard')
                ->with('status', 'Account disconnected.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.instagram.dashboard')
                ->withErrors(['disconnect' => $e->getMessage()]);
        }
    }
}
