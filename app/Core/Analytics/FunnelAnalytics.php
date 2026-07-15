<?php

namespace App\Core\Analytics;

use App\Models\Chatbot\Enterprise\AnalyticsEvent;
use App\Models\Chatbot\ChatbotLead;
use App\Models\Chatbot\ChatbotConversation;
use App\Models\Chatbot\Enterprise\Deal;
use Illuminate\Support\Facades\DB;

class FunnelAnalytics
{
    public function compute(string $period = 'all'): array
    {
        $dateFilter = match ($period) {
            'today' => now()->startOfDay(),
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            default => now()->subYears(100),
        };

        $sessions = AnalyticsEvent::where('event_type', 'page_view')
            ->when($period !== 'all', fn ($q) => $q->where('created_at', '>=', $dateFilter))
            ->distinct('session_id')
            ->count('session_id');

        $leads = ChatbotLead::where('status', '!=', 'spam')
            ->when($period !== 'all', fn ($q) => $q->where('created_at', '>=', $dateFilter))
            ->count();

        $newLeads = ChatbotLead::where('status', '!=', 'spam')
            ->when($period !== 'all', fn ($q) => $q->where('created_at', '>=', $dateFilter))
            ->where('created_at', '>=', now()->subDay())
            ->count();

        $conversations = ChatbotConversation::where('status', '!=', 'closed')
            ->when($period !== 'all', fn ($q) => $q->where('created_at', '>=', $dateFilter))
            ->count();

        $deals = Deal::when($period !== 'all', fn ($q) => $q->where('created_at', '>=', $dateFilter))
            ->count();

        $wonDeals = Deal::where('status', 'won')
            ->when($period !== 'all', fn ($q) => $q->where('created_at', '>=', $dateFilter))
            ->count();

        $leadToDealRate = $leads > 0 ? round(($deals / $leads) * 100, 1) : 0;
        $dealWinRate = $deals > 0 ? round(($wonDeals / $deals) * 100, 1) : 0;

        // Daily trend
        $days = match ($period) {
            'today' => 1,
            'week' => 7,
            'month' => 30,
            default => 30,
        };

        $dailyLabels = [];
        $dailySessions = [];
        $dailyLeads = [];
        $dailyConversions = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('M j');
            $dayStart = now()->subDays($i)->startOfDay();
            $dayEnd = now()->subDays($i)->endOfDay();

            $dailyLabels[] = $date;
            $dailySessions[] = AnalyticsEvent::where('event_type', 'page_view')
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->distinct('session_id')
                ->count('session_id');
            $dailyLeads[] = ChatbotLead::where('status', '!=', 'spam')
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->count();
            $dailyConversions[] = Deal::whereBetween('created_at', [$dayStart, $dayEnd])->count();
        }

        return [
            'sessions' => $sessions,
            'leads' => $leads,
            'new_leads' => $newLeads,
            'conversations' => $conversations,
            'deals' => $deals,
            'won_deals' => $wonDeals,
            'lead_to_deal_rate' => $leadToDealRate,
            'deal_win_rate' => $dealWinRate,
            'daily_labels' => $dailyLabels,
            'daily_sessions' => $dailySessions,
            'daily_leads' => $dailyLeads,
            'daily_conversions' => $dailyConversions,
        ];
    }

    public function trackPageView(string $sessionId, ?string $url = null): void
    {
        AnalyticsEvent::create([
            'event_type' => 'page_view',
            'session_id' => $sessionId,
            'metadata' => array_filter(['url' => $url, 'referrer' => request()->header('referer')]),
        ]);
    }

    public function track(string $sessionId, string $eventType, array $metadata = []): void
    {
        AnalyticsEvent::create([
            'event_type' => $eventType,
            'session_id' => $sessionId,
            'metadata' => $metadata,
        ]);
    }
}
