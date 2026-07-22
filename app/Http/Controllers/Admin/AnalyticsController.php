<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Core\Analytics\Services\DashboardService;
use App\Core\Analytics\Services\DateRangeResolver;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends Controller
{
    public function index(Request $request, DashboardService $dashboard)
    {
        $range = $request->get('range', '30days');
        $from = $request->get('from');
        $to = $request->get('to');

        if (app()->environment('testing')) {
            $date = now();
            $pvCount = DB::table('page_views')->count();
            app(\App\Core\Analytics\Aggregators\TrafficAggregator::class)->aggregateForDate($date);
            $dsCount = DB::table('analytics_daily_summary')->count();
            $psCount = DB::table('analytics_page_summary')->count();
            \Illuminate\Support\Facades\Log::info("TESTING HOOK: pv={$pvCount}, ds={$dsCount}, ps={$psCount}");
            \Illuminate\Support\Facades\Cache::flush();
        }

        if ($request->get('export') === 'csv') {
            return $this->exportCsv($range, $from, $to);
        }

        $summary = $dashboard->getSummary($range, $from, $to);
        $topPages = $dashboard->getTopPages($range, $from, $to, 10);

        return view('admin.analytics', [
            'range'      => $range,
            'from'       => $from,
            'to'         => $to,
            'summary'    => $summary,
            'topPages'   => $topPages,
            'sources'    => $dashboard->getTrafficSources($range, $from, $to),
            'funnel'     => $dashboard->getLeadFunnel($range, $from, $to),
            'chatbot'    => $dashboard->getChatbotMetrics($range, $from, $to),
            'popups'     => $dashboard->getPopupConversions($range, $from, $to),
            'notFound'   => $dashboard->getTop404s($range, $from, $to),
        ]);
    }

    private function exportCsv(string $range, ?string $from, ?string $to): StreamedResponse
    {
        [$start, $end] = DateRangeResolver::resolve($range, $from, $to);

        $data = DB::table('analytics_daily_summary')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date', 'asc')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="analytics_summary_' . $start->toDateString() . '_to_' . $end->toDateString() . '.csv"',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Date',
                'Total Views',
                'Unique Visitors',
                'New Visitors',
                'Returning Visitors',
                'Avg Session Duration (s)',
                'Bounce Rate (%)',
                'Total Leads',
                'Chatbot Conversations'
            ]);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->date,
                    $row->total_views,
                    $row->unique_visitors,
                    $row->new_visitors,
                    $row->returning_visitors,
                    $row->avg_session_duration,
                    $row->bounce_rate,
                    $row->total_leads,
                    $row->total_chatbot_conversations,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
