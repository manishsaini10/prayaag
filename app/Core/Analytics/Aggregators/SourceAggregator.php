<?php

namespace App\Core\Analytics\Aggregators;

use App\Models\AnalyticsSourceSummary;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SourceAggregator
{
    public function aggregateForDate(Carbon $date): void
    {
        $dateStr = $date->toDateString();
        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();

        $sources = [];

        // 1. Aggregate from Chatbot Visitor Sessions (which have rich UTM tags)
        $sessions = DB::table('chatbot_visitor_sessions')
            ->select('utm_source', 'utm_medium', 'utm_campaign', DB::raw('count(*) as visits'))
            ->whereBetween('started_at', [$start, $end])
            ->groupBy('utm_source', 'utm_medium', 'utm_campaign')
            ->get();

        foreach ($sessions as $session) {
            $src = $session->utm_source ?: 'direct';
            $med = $session->utm_medium ?: 'none';
            $camp = $session->utm_campaign ?: 'none';
            $key = "{$src}|{$med}|{$camp}";

            if (!isset($sources[$key])) {
                $sources[$key] = [
                    'source'          => $src,
                    'medium'          => $med,
                    'campaign'        => $camp,
                    'visits'          => 0,
                    'leads_generated' => 0,
                ];
            }
            $sources[$key]['visits'] += $session->visits;
        }

        // 2. Aggregate from Chatbot Leads (associating with visitor session UTM tags)
        $leads = DB::table('chatbot_leads')
            ->join('chatbot_visitor_sessions', 'chatbot_leads.visitor_id', '=', 'chatbot_visitor_sessions.visitor_id')
            ->select('chatbot_visitor_sessions.utm_source', 'chatbot_visitor_sessions.utm_medium', 'chatbot_visitor_sessions.utm_campaign', DB::raw('count(*) as count'))
            ->whereBetween('chatbot_leads.created_at', [$start, $end])
            ->groupBy('chatbot_visitor_sessions.utm_source', 'chatbot_visitor_sessions.utm_medium', 'chatbot_visitor_sessions.utm_campaign')
            ->get();

        foreach ($leads as $lead) {
            $src = $lead->utm_source ?: 'direct';
            $med = $lead->utm_medium ?: 'none';
            $camp = $lead->utm_campaign ?: 'none';
            $key = "{$src}|{$med}|{$camp}";

            if (!isset($sources[$key])) {
                $sources[$key] = [
                    'source'          => $src,
                    'medium'          => $med,
                    'campaign'        => $camp,
                    'visits'          => 0,
                    'leads_generated' => 0,
                ];
            }
            $sources[$key]['leads_generated'] += $lead->count;
        }

        // 3. Aggregate Page Views (Referrers)
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        $pageViews = DB::table('page_views')
            ->select('referrer', DB::raw('count(*) as visits'))
            ->whereBetween('viewed_at', [$start, $end])
            ->whereNotNull('referrer')
            ->where('referrer', '!=', '')
            ->groupBy('referrer')
            ->get();

        foreach ($pageViews as $pv) {
            [$src, $med] = $this->parseReferrer($pv->referrer, $appHost);
            if ($src === 'internal') {
                continue;
            }
            $camp = 'none';
            $key = "{$src}|{$med}|{$camp}";

            if (!isset($sources[$key])) {
                $sources[$key] = [
                    'source'          => $src,
                    'medium'          => $med,
                    'campaign'        => $camp,
                    'visits'          => 0,
                    'leads_generated' => 0,
                ];
            }
            $sources[$key]['visits'] += $pv->visits;
        }

        // 4. Save/Update summaries
        foreach ($sources as $key => $data) {
            AnalyticsSourceSummary::updateOrCreate(
                [
                    'date'     => $dateStr,
                    'source'   => mb_substr($data['source'], 0, 255),
                    'medium'   => $data['medium'] ? mb_substr($data['medium'], 0, 255) : null,
                    'campaign' => $data['campaign'] ? mb_substr($data['campaign'], 0, 255) : null,
                ],
                [
                    'visits'          => $data['visits'],
                    'leads_generated' => $data['leads_generated'],
                ]
            );
        }
    }

    private function parseReferrer(string $referrer, ?string $appHost): array
    {
        $host = parse_url($referrer, PHP_URL_HOST);
        if (!$host) {
            return ['direct', 'none'];
        }
        $host = str_replace('www.', '', strtolower($host));

        if ($appHost && str_replace('www.', '', strtolower($appHost)) === $host) {
            return ['internal', 'none'];
        }

        if (str_contains($host, 'google.')) {
            return ['google', 'organic'];
        }
        if (str_contains($host, 'bing.')) {
            return ['bing', 'organic'];
        }
        if (str_contains($host, 'yahoo.')) {
            return ['yahoo', 'organic'];
        }
        if (str_contains($host, 'facebook.com') || str_contains($host, 'm.facebook.com') || str_contains($host, 'l.facebook.com')) {
            return ['facebook', 'social'];
        }
        if (str_contains($host, 'instagram.com')) {
            return ['instagram', 'social'];
        }
        if (str_contains($host, 'linkedin.com') || str_contains($host, 'lnkd.in')) {
            return ['linkedin', 'social'];
        }
        if (str_contains($host, 't.co') || str_contains($host, 'twitter.com') || str_contains($host, 'x.com')) {
            return ['twitter', 'social'];
        }

        return [$host, 'referral'];
    }
}
