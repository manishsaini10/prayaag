<?php

namespace App\Core\Chatbot\Services;

use App\Models\Chatbot\ChatbotVisitor;
use App\Models\Chatbot\Enterprise\VisitorSession;
use App\Models\Chatbot\Enterprise\VisitorDevice;
use App\Models\Chatbot\Enterprise\VisitorLocation;
use App\Models\Chatbot\Enterprise\VisitorPage;
use App\Models\Chatbot\Enterprise\VisitorEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VisitorTrackingService
{
    public function identifyVisitor(Request $request): array
    {
        $sessionToken = $request->input('session_id') ?? Str::random(32);
        $ip = $request->ip();
        $agent = $request->userAgent();

        $visitor = ChatbotVisitor::firstOrCreate(
            ['session_id' => $sessionToken],
            [
                'ip_address' => $ip,
                'landing_page' => $request->input('landing_page', url()->current()),
                'referrer' => $request->input('referrer'),
                'utm_source' => $request->input('utm_source'),
                'utm_medium' => $request->input('utm_medium'),
                'utm_campaign' => $request->input('utm_campaign'),
            ]
        );

        $visitor->update(['current_page' => $request->input('current_page', url()->current())]);

        $session = VisitorSession::firstOrCreate(
            ['session_token' => $sessionToken],
            [
                'visitor_id' => $visitor->id,
                'started_at' => now(),
                'entry_page' => $request->input('landing_page', url()->current()),
                'referrer' => $request->input('referrer'),
                'utm_source' => $request->input('utm_source'),
                'utm_medium' => $request->input('utm_medium'),
                'utm_campaign' => $request->input('utm_campaign'),
            ]
        );

        if ($session->ended_at) {
            $session->update(['ended_at' => null, 'is_active' => true]);
        }

        $this->updateDeviceInfo($visitor, $agent);
        $this->updateGeoLocation($visitor, $ip);

        return [
            'visitor' => $visitor,
            'session' => $session,
        ];
    }

    public function trackPageView(Request $request, ChatbotVisitor $visitor, VisitorSession $session): VisitorPage
    {
        if (!$visitor->exists) $visitor = ChatbotVisitor::findOrFail($visitor->id);
        if (!$session->exists) $session = VisitorSession::findOrFail($session->id);

        $visitor->update(['current_page' => $request->input('url', url()->current())]);

        $page = VisitorPage::create([
            'visitor_id' => $visitor->id,
            'session_id' => $session->id,
            'url' => $request->input('url', url()->current()),
            'title' => $request->input('title'),
            'referrer' => $request->input('referrer'),
            'visited_at' => now(),
        ]);

        $session->increment('page_views');
        $session->update(['exit_page' => $request->input('url', url()->current())]);

        return $page;
    }

    public function trackEvent(Request $request, ChatbotVisitor $visitor, ?VisitorSession $session = null): VisitorEvent
    {
        $event = VisitorEvent::create([
            'visitor_id' => $visitor->id,
            'session_id' => $session?->id,
            'event_type' => $request->input('event_type', 'custom'),
            'event_category' => $request->input('event_category', 'interaction'),
            'event_label' => $request->input('event_label'),
            'event_value' => $request->input('event_value'),
            'page_url' => $request->input('page_url', url()->current()),
            'metadata' => $request->input('metadata', []),
            'occurred_at' => now(),
        ]);

        return $event;
    }

    public function endSession(VisitorSession $session): void
    {
        $session->update([
            'ended_at' => now(),
            'is_active' => false,
            'duration_seconds' => $session->started_at ? now()->diffInSeconds($session->started_at) : 0,
        ]);
    }

    public function heartbeat(Request $request, ChatbotVisitor $visitor, VisitorSession $session): void
    {
        $duration = $session->started_at ? now()->diffInSeconds($session->started_at) : 0;
        $session->update(['duration_seconds' => $duration]);
    }

    public function updateDeviceInfo(ChatbotVisitor $visitor, ?string $userAgent): void
    {
        if (!$userAgent) return;

        $deviceType = 'Desktop';
        if (preg_match('/(android|iphone|ipad|ipod|mobile)/i', $userAgent)) {
            $deviceType = preg_match('/(ipad|tablet)/i', $userAgent) ? 'Tablet' : 'Mobile';
        }

        $browser = 'Chrome';
        if (preg_match('/firefox/i', $userAgent)) $browser = 'Firefox';
        elseif (preg_match('/edge/i', $userAgent)) $browser = 'Edge';
        elseif (preg_match('/opr/i', $userAgent)) $browser = 'Opera';
        elseif (preg_match('/safari/i', $userAgent) && !preg_match('/chrome/i', $userAgent)) $browser = 'Safari';

        $os = 'Windows';
        if (preg_match('/mac/i', $userAgent)) $os = 'macOS';
        elseif (preg_match('/linux/i', $userAgent)) $os = 'Linux';
        elseif (preg_match('/android/i', $userAgent)) $os = 'Android';
        elseif (preg_match('/iphone|ipad/i', $userAgent)) $os = 'iOS';

        $deviceName = $deviceType;
        if ($deviceType === 'Mobile' && preg_match('/iphone/i', $userAgent)) $deviceName = 'iPhone';
        elseif ($deviceType === 'Mobile' && preg_match('/android/i', $userAgent)) $deviceName = 'Android Phone';
        elseif ($deviceType === 'Tablet' && preg_match('/ipad/i', $userAgent)) $deviceName = 'iPad';

        $visitor->update([
            'device' => $deviceType,
            'browser' => $browser,
            'os' => $os,
        ]);

        VisitorDevice::updateOrCreate(
            ['visitor_id' => $visitor->id],
            [
                'device_type' => $deviceType,
                'device_name' => $deviceName,
                'browser' => $browser,
                'browser_version' => $this->parseBrowserVersion($userAgent, $browser),
                'os' => $os,
                'os_version' => $this->parseOsVersion($userAgent, $os),
                'user_agent' => $userAgent,
                'ip_address' => $visitor->ip_address,
            ]
        );
    }

    public function updateGeoLocation(ChatbotVisitor $visitor, ?string $ip): void
    {
        if (!$ip || $ip === '127.0.0.1' || $ip === '::1') return;

        $existing = VisitorLocation::where('visitor_id', $visitor->id)->first();
        if ($existing) return;

        $location = $this->lookupIp($ip);
        if ($location) {
            VisitorLocation::create(array_merge(
                ['visitor_id' => $visitor->id],
                $location
            ));

            $visitor->update([
                'country' => $location['country'] ?? null,
                'city' => $location['city'] ?? null,
            ]);
        }
    }

    private function lookupIp(string $ip): ?array
    {
        try {
            $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=status,country,countryCode,region,city,zip,lat,lon,isp,org", false, stream_context_create([
                'http' => ['timeout' => 3],
            ]));

            if ($response === false) return null;

            $data = json_decode($response, true);
            if (!($data['status'] ?? '') === 'success') return null;

            return [
                'country' => $data['country'] ?? null,
                'country_code' => $data['countryCode'] ?? null,
                'region' => $data['region'] ?? null,
                'city' => $data['city'] ?? null,
                'zip' => $data['zip'] ?? null,
                'latitude' => $data['lat'] ?? null,
                'longitude' => $data['lon'] ?? null,
                'isp' => $data['isp'] ?? null,
                'organization' => $data['org'] ?? null,
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseBrowserVersion(string $ua, string $browser): ?string
    {
        $patterns = [
            'Firefox' => '/Firefox\/([0-9.]+)/',
            'Edge' => '/Edg\/([0-9.]+)/',
            'Opera' => '/OPR\/([0-9.]+)/',
            'Chrome' => '/Chrome\/([0-9.]+)/',
            'Safari' => '/Version\/([0-9.]+)/',
        ];
        $pattern = $patterns[$browser] ?? null;
        if (!$pattern || !preg_match($pattern, $ua, $m)) return null;
        return $m[1];
    }

    private function parseOsVersion(string $ua, string $os): ?string
    {
        $patterns = [
            'Windows' => '/Windows NT ([0-9.]+)/',
            'macOS' => '/Mac OS X ([0-9._]+)/',
            'Android' => '/Android ([0-9.]+)/',
            'iOS' => '/OS ([0-9._]+) like Mac/',
        ];
        $pattern = $patterns[$os] ?? null;
        if (!$pattern || !preg_match($pattern, $ua, $m)) return null;
        return str_replace('_', '.', $m[1]);
    }

    public function getOnlineVisitors(): array
    {
        $activeSessions = VisitorSession::where('is_active', true)
            ->where('started_at', '>=', now()->subMinutes(5))
            ->with('visitor')
            ->get();

        return [
            'count' => $activeSessions->count(),
            'visitors' => $activeSessions->map(fn($s) => [
                'id' => $s->visitor->id,
                'name' => $s->visitor->name ?? 'Visitor',
                'session_token' => $s->session_token,
                'current_page' => $s->visitor->current_page,
                'country' => $s->visitor->country,
                'device' => $s->visitor->device,
                'browser' => $s->visitor->browser,
                'page_views' => $s->page_views,
                'duration' => $s->duration_seconds,
                'started_at' => $s->started_at,
            ]),
        ];
    }

    public function getVisitorTimeline(ChatbotVisitor $visitor, int $limit = 50): array
    {
        $visitor->loadMissing(['sessions.pages', 'sessions.events', 'conversations.messages']);

        $timeline = [];

        foreach ($visitor->sessions as $session) {
            foreach ($session->pages as $page) {
                $timeline[] = [
                    'type' => 'page_view',
                    'label' => $page->title ?: $page->url,
                    'url' => $page->url,
                    'time' => $page->visited_at,
                ];
            }
            foreach ($session->events as $event) {
                $timeline[] = [
                    'type' => $event->event_type,
                    'label' => $event->event_label,
                    'category' => $event->event_category,
                    'time' => $event->occurred_at,
                ];
            }
        }

        foreach ($visitor->conversations as $conv) {
            $timeline[] = [
                'type' => 'conversation',
                'label' => "Conversation #{$conv->short_id}",
                'status' => $conv->status,
                'time' => $conv->created_at,
            ];
        }

        usort($timeline, fn($a, $b) => strtotime($b['time']) - strtotime($a['time']));

        return array_slice($timeline, 0, $limit);
    }

    public function getAdminStats(): array
    {
        $today = now()->startOfDay();

        return [
            'total_visitors' => ChatbotVisitor::count(),
            'visitors_today' => ChatbotVisitor::where('created_at', '>=', $today)->count(),
            'active_sessions' => VisitorSession::where('is_active', true)
                ->where('started_at', '>=', now()->subMinutes(5))->count(),
            'total_page_views' => VisitorPage::count(),
            'page_views_today' => VisitorPage::where('visited_at', '>=', $today)->count(),
            'total_events' => VisitorEvent::count(),
            'unique_countries' => VisitorLocation::distinct('country')->count('country'),
            'top_pages' => VisitorPage::selectRaw('url, title, COUNT(*) as visits')
                ->groupBy('url', 'title')
                ->orderByDesc('visits')
                ->limit(10)
                ->get(),
            'device_breakdown' => VisitorDevice::selectRaw('device_type, COUNT(*) as count')
                ->groupBy('device_type')
                ->pluck('count', 'device_type'),
            'browser_breakdown' => ChatbotVisitor::selectRaw('browser, COUNT(*) as count')
                ->whereNotNull('browser')
                ->groupBy('browser')
                ->pluck('count', 'browser'),
        ];
    }
}
