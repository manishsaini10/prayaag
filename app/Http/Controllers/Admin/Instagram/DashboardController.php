<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Instagram;

use App\Http\Controllers\Controller;
use App\Jobs\SyncInstagramMedia;
use App\Models\InstagramAccount;
use App\Services\Instagram\InstagramService;
use App\Services\Instagram\InstagramOEmbedService;
use App\Services\Instagram\PublicInstagramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly InstagramService $instagram,
        private readonly PublicInstagramService $publicInstagram,
        private readonly InstagramOEmbedService $oEmbed,
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

    /**
     * 1-Click Connect by Instagram Handle (No Meta App / .env setup required)
     */
    public function connectHandle(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => 'required|string|min:2|max:100',
        ]);

        try {
            $account = $this->publicInstagram->connectByUsername(
                $request->input('username'),
                $request->input('manual_token')
            );

            return redirect()->route('admin.instagram.dashboard')
                ->with('status', "Instagram account @{$account->username} connected successfully! Posts are now live on website.");
        } catch (\Throwable $e) {
            return redirect()->route('admin.instagram.dashboard')
                ->withErrors(['connect' => 'Failed to connect Instagram: ' . $e->getMessage()]);
        }
    }

    /**
     * Save / Update Direct Instagram Access Token without touching .env
     */
    public function saveToken(Request $request): RedirectResponse
    {
        $request->validate([
            'username'     => 'required|string',
            'access_token' => 'required|string|min:10',
        ]);

        try {
            $account = $this->publicInstagram->connectByUsername(
                $request->input('username'),
                $request->input('access_token')
            );

            return redirect()->route('admin.instagram.dashboard')
                ->with('status', "Access token saved for @{$account->username} and posts synced successfully!");
        } catch (\Throwable $e) {
            return redirect()->route('admin.instagram.dashboard')
                ->withErrors(['save_token' => 'Failed to save token: ' . $e->getMessage()]);
        }
    }

    /**
     * Add Instagram post URLs directly (oEmbed — No API Key, No Secret, No OAuth)
     * Like Insta Gallery WordPress plugin — just paste post URLs.
     */
    public function addPostUrls(Request $request): RedirectResponse
    {
        $request->validate([
            'account_id' => 'required|string|exists:instagram_accounts,id',
            'post_urls'  => 'required|string|min:5',
        ]);

        $account = InstagramAccount::findOrFail($request->input('account_id'));

        // Parse textarea — one URL per line
        $urls = array_filter(
            array_map('trim', explode("\n", $request->input('post_urls'))),
            fn ($u) => !empty($u) && str_contains($u, 'instagram.com')
        );

        if (empty($urls)) {
            return redirect()->route('admin.instagram.dashboard')
                ->withErrors(['post_urls' => 'Please enter valid Instagram post URLs (instagram.com/p/... or instagram.com/reel/...)']);
        }

        try {
            $result = $this->oEmbed->savePostUrls($account, array_values($urls));

            return redirect()->route('admin.instagram.dashboard')
                ->with('status', "✅ {$result['saved']} Instagram posts added to website feed!" .
                    ($result['failed'] > 0 ? " ({$result['failed']} invalid URLs skipped)" : ''));
        } catch (\Throwable $e) {
            return redirect()->route('admin.instagram.dashboard')
                ->withErrors(['post_urls' => 'Failed: ' . $e->getMessage()]);
        }
    }

    public function sync(string $id): RedirectResponse
    {
        $account = InstagramAccount::findOrFail($id);

        // If it's a public / 1-click account, use PublicInstagramService
        if (str_starts_with($account->instagram_business_id, 'public_') || empty(config('instagram.app_id'))) {
            $this->publicInstagram->fetchPublicPosts($account);
            return redirect()->route('admin.instagram.dashboard')
                ->with('status', "Feed for @{$account->username} synced successfully.");
        }

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
        $accounts = InstagramAccount::connected()->get();

        foreach ($accounts as $account) {
            if (str_starts_with($account->instagram_business_id, 'public_') || empty(config('instagram.app_id'))) {
                $this->publicInstagram->fetchPublicPosts($account);
            } elseif (config('instagram.enable_queue')) {
                SyncInstagramMedia::dispatch($account->id);
            } else {
                $this->instagram->sync($account->id);
            }
        }

        return redirect()->route('admin.instagram.dashboard')
            ->with('status', 'Sync completed for all connected Instagram accounts.');
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
