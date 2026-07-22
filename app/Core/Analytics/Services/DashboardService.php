<?php

namespace App\Core\Analytics\Services;

use App\Core\Analytics\Services\DateRangeResolver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardService
{
    public function getSummary(string $range, ?string $from = null, ?string $to = null): array
    {
        [$start, $end] = DateRangeResolver::resolve($range, $from, $to);
        $cacheKey = "analytics:summary:{$range}:" . $start->toDateString() . ":" . $end->toDateString();

        return Cache::remember($cacheKey, 900, function () use ($start, $end) {
            $stats = DB::table('analytics_daily_summary')
                ->where('date', '>=', $start->toDateString())
                ->where('date', '<=', $end->toDateString() . ' 23:59:59')
                ->select(
                    DB::raw('SUM(total_views) as total_views'),
                    DB::raw('SUM(unique_visitors) as unique_visitors'),
                    DB::raw('SUM(total_leads) as total_leads'),
                    DB::raw('SUM(total_chatbot_conversations) as total_chatbot_conversations'),
                    DB::raw('AVG(avg_session_duration) as avg_session_duration'),
                    DB::raw('AVG(bounce_rate) as bounce_rate')
                )
                ->first();

            // Daily trend details for chart
            $trend = DB::table('analytics_daily_summary')
                ->where('date', '>=', $start->toDateString())
                ->where('date', '<=', $end->toDateString() . ' 23:59:59')
                ->orderBy('date', 'asc')
                ->get(['date', 'total_views', 'unique_visitors']);

            $labels = [];
            $views = [];
            $visitors = [];

            foreach ($trend as $row) {
                $labels[] = Carbon::parse($row->date)->format('M j');
                $views[] = (int) $row->total_views;
                $visitors[] = (int) $row->unique_visitors;
            }

            // Fallback to empty datasets if no summary data exists yet
            if (empty($labels)) {
                $labels[] = now()->format('M j');
                $views[] = 0;
                $visitors[] = 0;
            }

            return [
                'total_views'                 => (int) ($stats->total_views ?? 0),
                'unique_visitors'             => (int) ($stats->unique_visitors ?? 0),
                'total_leads'                 => (int) ($stats->total_leads ?? 0),
                'total_chatbot_conversations' => (int) ($stats->total_chatbot_conversations ?? 0),
                'avg_session_duration'        => (int) round($stats->avg_session_duration ?? 0),
                'bounce_rate'                 => round($stats->bounce_rate ?? 0, 2),
                'chart' => [
                    'labels'   => $labels,
                    'views'    => $views,
                    'visitors' => $visitors,
                ]
            ];
        });
    }

    public function getTopPages(string $range, ?string $from = null, ?string $to = null, int $limit = 10): array
    {
        [$start, $end] = DateRangeResolver::resolve($range, $from, $to);
        $cacheKey = "analytics:toppages:{$range}:{$limit}:" . $start->toDateString() . ":" . $end->toDateString();

        return Cache::remember($cacheKey, 900, function () use ($start, $end, $limit) {
            return DB::table('analytics_page_summary')
                ->where('date', '>=', $start->toDateString())
                ->where('date', '<=', $end->toDateString() . ' 23:59:59')
                ->select('url', DB::raw('SUM(views) as views'), DB::raw('SUM(unique_views) as unique_views'))
                ->groupBy('url')
                ->orderByDesc('views')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    public function getTrafficSources(string $range, ?string $from = null, ?string $to = null): array
    {
        [$start, $end] = DateRangeResolver::resolve($range, $from, $to);
        $cacheKey = "analytics:sources:{$range}:" . $start->toDateString() . ":" . $end->toDateString();

        return Cache::remember($cacheKey, 900, function () use ($start, $end) {
            $data = DB::table('analytics_source_summary')
                ->where('date', '>=', $start->toDateString())
                ->where('date', '<=', $end->toDateString() . ' 23:59:59')
                ->select('source', DB::raw('SUM(visits) as visits'), DB::raw('SUM(leads_generated) as leads'))
                ->groupBy('source')
                ->orderByDesc('visits')
                ->get();

            $labels = [];
            $visits = [];
            $leads = [];

            foreach ($data as $row) {
                $labels[] = $row->source;
                $visits[] = (int) $row->visits;
                $leads[] = (int) $row->leads;
            }

            return [
                'labels' => $labels,
                'visits' => $visits,
                'leads'  => $leads,
                'raw'    => $data->toArray()
            ];
        });
    }

    public function getLeadFunnel(string $range, ?string $from = null, ?string $to = null): array
    {
        [$start, $end] = DateRangeResolver::resolve($range, $from, $to);
        $startStr = $start->toDateTimeString();
        $endStr = $end->toDateTimeString();

        $cacheKey = "analytics:funnel:{$range}:" . $start->toDateString() . ":" . $end->toDateString();

        return Cache::remember($cacheKey, 900, function () use ($start, $end, $startStr, $endStr) {
            // Step 1: Total unique visitors
            $visitors = DB::table('analytics_daily_summary')
                ->where('date', '>=', $start->toDateString())
                ->where('date', '<=', $end->toDateString() . ' 23:59:59')
                ->sum('unique_visitors');

            // Step 2: Chatbot conversations
            $conversations = DB::table('chatbot_conversations')
                ->whereBetween('created_at', [$startStr, $endStr])
                ->count();

            // Step 3: Total Leads generated
            $chatbotLeads = DB::table('chatbot_leads')
                ->whereBetween('created_at', [$startStr, $endStr])
                ->count();

            $enquiries = DB::table('enquiries')
                ->whereBetween('created_at', [$startStr, $endStr])
                ->count();

            $formSubmissions = DB::table('form_submissions')
                ->whereBetween('created_at', [$startStr, $endStr])
                ->count();

            $leads = $chatbotLeads + $enquiries + $formSubmissions;

            // Step 4: Closed / Won Deals (Admissions)
            $admissions = DB::table('chatbot_deals')
                ->where('status', 'won')
                ->whereBetween('created_at', [$startStr, $endStr])
                ->count();

            // Calculate conversion rates
            $visitorToChatbotRate = $visitors > 0 ? round(($conversations / $visitors) * 100, 1) : 0;
            $chatbotToLeadRate = $conversations > 0 ? round(($chatbotLeads / $conversations) * 100, 1) : 0;
            $leadToAdmissionRate = $leads > 0 ? round(($admissions / $leads) * 100, 1) : 0;

            return [
                'visitors'               => (int) $visitors,
                'conversations'          => (int) $conversations,
                'leads'                  => (int) $leads,
                'admissions'             => (int) $admissions,
                'visitor_to_chatbot_pct' => $visitorToChatbotRate,
                'chatbot_to_lead_pct'    => $chatbotToLeadRate,
                'lead_to_admission_pct'  => $leadToAdmissionRate,
            ];
        });
    }

    public function getChatbotMetrics(string $range, ?string $from = null, ?string $to = null): array
    {
        [$start, $end] = DateRangeResolver::resolve($range, $from, $to);
        $startStr = $start->toDateTimeString();
        $endStr = $end->toDateTimeString();

        $cacheKey = "analytics:chatbot:{$range}:" . $start->toDateString() . ":" . $end->toDateString();

        return Cache::remember($cacheKey, 900, function () use ($startStr, $endStr) {
            $total = DB::table('chatbot_conversations')
                ->whereBetween('created_at', [$startStr, $endStr])
                ->count();

            $aiHandled = DB::table('chatbot_conversations')
                ->where('ai_handled', 1)
                ->whereBetween('created_at', [$startStr, $endStr])
                ->count();

            $avgRating = DB::table('chatbot_conversations')
                ->whereNotNull('rating')
                ->whereBetween('created_at', [$startStr, $endStr])
                ->avg('rating') ?? 0;

            $avgResponseTime = DB::table('chatbot_conversations')
                ->whereNotNull('response_time')
                ->whereBetween('created_at', [$startStr, $endStr])
                ->avg('response_time') ?? 0;

            $intents = DB::table('chatbot_conversations')
                ->select('intent', DB::raw('count(*) as count'))
                ->whereNotNull('intent')
                ->where('intent', '!=', '')
                ->whereBetween('created_at', [$startStr, $endStr])
                ->groupBy('intent')
                ->orderByDesc('count')
                ->limit(5)
                ->get()
                ->toArray();

            return [
                'total_conversations' => $total,
                'ai_handled_count'    => $aiHandled,
                'ai_handled_percent'  => $total > 0 ? round(($aiHandled / $total) * 100, 1) : 0,
                'avg_rating'          => round($avgRating, 1),
                'avg_response_time'   => round($avgResponseTime),
                'top_intents'         => $intents,
            ];
        });
    }

    public function getPopupConversions(string $range, ?string $from = null, ?string $to = null): array
    {
        [$start, $end] = DateRangeResolver::resolve($range, $from, $to);
        $startStr = $start->toDateTimeString();
        $endStr = $end->toDateTimeString();

        $cacheKey = "analytics:popups:{$range}:" . $start->toDateString() . ":" . $end->toDateString();

        return Cache::remember($cacheKey, 900, function () use ($startStr, $endStr) {
            $stats = DB::table('popup_analytics')
                ->join('popups', 'popup_analytics.popup_id', '=', 'popups.id')
                ->select(
                    'popups.title as popup_name',
                    DB::raw("SUM(CASE WHEN popup_analytics.event_type = 'impression' THEN 1 ELSE 0 END) as impressions"),
                    DB::raw("SUM(CASE WHEN popup_analytics.event_type = 'conversion' THEN 1 ELSE 0 END) as conversions")
                )
                ->whereBetween('popup_analytics.created_at', [$startStr, $endStr])
                ->groupBy('popups.id', 'popups.title')
                ->get();

            $results = [];
            foreach ($stats as $row) {
                $rate = $row->impressions > 0 ? round(($row->conversions / $row->impressions) * 100, 2) : 0;
                $results[] = [
                    'name'            => $row->popup_name,
                    'impressions'     => (int) $row->impressions,
                    'conversions'     => (int) $row->conversions,
                    'conversion_rate' => $rate,
                ];
            }

            return $results;
        });
    }

    public function getTop404s(string $range, ?string $from = null, ?string $to = null): array
    {
        [$start, $end] = DateRangeResolver::resolve($range, $from, $to);
        $startStr = $start->toDateTimeString();
        $endStr = $end->toDateTimeString();

        $cacheKey = "analytics:404s:{$range}:" . $start->toDateString() . ":" . $end->toDateString();

        return Cache::remember($cacheKey, 900, function () use ($startStr, $endStr) {
            return DB::table('not_found_logs')
                ->select('path', DB::raw('count(*) as count'), DB::raw('max(created_at) as last_seen'))
                ->whereBetween('created_at', [$startStr, $endStr])
                ->groupBy('path')
                ->orderByDesc('count')
                ->limit(5)
                ->get()
                ->toArray();
        });
    }
}
