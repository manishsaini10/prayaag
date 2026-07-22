<?php

namespace App\Core\Analytics\Aggregators;

use App\Models\AnalyticsDailySummary;
use App\Models\AnalyticsPageSummary;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TrafficAggregator
{
    public function aggregateForDate(Carbon $date): void
    {
        $dateStr = $date->toDateString();
        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();

        // 1. Total and Unique views from page_views
        $totalViews = DB::table('page_views')
            ->whereBetween('viewed_at', [$start, $end])
            ->count();

        $uniqueIPsOnDay = DB::table('page_views')
            ->whereBetween('viewed_at', [$start, $end])
            ->whereNotNull('ip_hash')
            ->distinct()
            ->pluck('ip_hash')
            ->toArray();

        $newVisitors = 0;
        $returningVisitors = 0;
        $uniqueVisitors = count($uniqueIPsOnDay);

        if ($uniqueVisitors > 0) {
            $returningIPs = DB::table('page_views')
                ->where('viewed_at', '<', $start)
                ->whereIn('ip_hash', $uniqueIPsOnDay)
                ->distinct()
                ->pluck('ip_hash')
                ->toArray();

            $returningVisitors = count($returningIPs);
            $newVisitors = $uniqueVisitors - $returningVisitors;
        }

        // 2. Average Session Duration
        $avgSessionDuration = DB::table('chatbot_visitor_sessions')
            ->whereBetween('started_at', [$start, $end])
            ->avg('duration_seconds') ?? 0;

        // 3. Bounce Rate
        $totalSessions = DB::table('chatbot_visitor_sessions')
            ->whereBetween('started_at', [$start, $end])
            ->count();

        $bounces = DB::table('chatbot_visitor_sessions')
            ->whereBetween('started_at', [$start, $end])
            ->where('page_views', '<=', 1)
            ->count();

        $bounceRate = $totalSessions > 0 ? round(($bounces / $totalSessions) * 100, 2) : 0;

        // 4. Lead calculations
        $chatbotLeads = DB::table('chatbot_leads')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $enquiries = DB::table('enquiries')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $formSubmissions = DB::table('form_submissions')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $totalLeads = $chatbotLeads + $enquiries + $formSubmissions;

        // 5. Chatbot conversations
        $totalChatbotConversations = DB::table('chatbot_conversations')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        // 6. Update or Create Daily Summary
        AnalyticsDailySummary::updateOrCreate(
            ['date' => $dateStr],
            [
                'total_views'                 => $totalViews,
                'unique_visitors'             => $uniqueVisitors,
                'new_visitors'                => $newVisitors,
                'returning_visitors'          => $returningVisitors,
                'avg_session_duration'        => (int) round($avgSessionDuration),
                'bounce_rate'                 => $bounceRate,
                'total_leads'                 => $totalLeads,
                'total_chatbot_conversations' => $totalChatbotConversations,
            ]
        );

        // 7. Aggregate page views into page summary
        $pagesSummary = DB::table('page_views')
            ->select('page_id', 'path as url', DB::raw('count(*) as views'), DB::raw('count(distinct ip_hash) as unique_views'))
            ->whereBetween('viewed_at', [$start, $end])
            ->groupBy('page_id', 'path')
            ->get();

        foreach ($pagesSummary as $row) {
            AnalyticsPageSummary::updateOrCreate(
                [
                    'date'    => $dateStr,
                    'url'     => mb_substr($row->url, 0, 500),
                    'page_id' => $row->page_id,
                ],
                [
                    'views'            => $row->views,
                    'unique_views'     => $row->unique_views,
                    'avg_time_on_page' => 0, // Not explicitly tracked in raw logs, defaults to 0
                ]
            );
        }
    }
}
