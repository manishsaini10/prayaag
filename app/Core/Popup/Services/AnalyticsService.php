<?php

namespace App\Core\Popup\Services;

use App\Core\Popup\DTOs\AnalyticsDTO;
use App\Models\Popup\Popup;
use App\Models\Popup\PopupAnalytics;
use App\Models\Popup\PopupLead;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function __construct(
        private readonly PopupAnalytics $model,
        private readonly PopupLead $leadModel,
    ) {}

    public function track(AnalyticsDTO $dto): void
    {
        $data = $dto->toArray();

        if (config('popup-builder.analytics.ip_anonymization', true) && $data['ip_address']) {
            $parts = explode('.', $data['ip_address']);
            if (count($parts) === 4) {
                $parts[3] = '0';
                $data['ip_address'] = implode('.', $parts);
            }
        }

        $this->model->create($data);

        // Update popup counters
        Popup::where('id', $dto->popupId)->increment($dto->eventType . '_count');
        $this->clearCache($dto->popupId);
    }

    public function getPopupStats(string $popupId, string $period = '7d'): array
    {
        $cacheKey = "popup:stats:{$popupId}:{$period}";
        return Cache::remember($cacheKey, 300, function () use ($popupId, $period) {
            $range = $this->getDateRange($period);
            return [
                'views' => $this->countByEvent($popupId, 'view', $range),
                'impressions' => $this->countByEvent($popupId, 'impression', $range),
                'clicks' => $this->countByEvent($popupId, 'click', $range),
                'conversions' => $this->countByEvent($popupId, 'conversion', $range),
                'close' => $this->countByEvent($popupId, 'close', $range),
                'ctr' => $this->calculateRate($popupId, 'click', 'impression', $range),
                'conversion_rate' => $this->calculateRate($popupId, 'conversion', 'view', $range),
            ];
        });
    }

    public function getDailyStats(string $popupId, string $eventType, string $period = '30d'): Collection
    {
        $range = $this->getDateRange($period);
        return $this->model
            ->select(
                DB::raw('DATE(occurred_at) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->where('popup_id', $popupId)
            ->where('event_type', $eventType)
            ->whereBetween('occurred_at', [$range['start'], $range['end']])
            ->groupBy(DB::raw('DATE(occurred_at)'))
            ->orderBy('date')
            ->get();
    }

    public function getTopPopups(int $limit = 10, string $period = '30d'): Collection
    {
        $range = $this->getDateRange($period);
        return $this->model
            ->select(
                'popup_id',
                DB::raw('COUNT(*) as total_views'),
                DB::raw("SUM(CASE WHEN event_type = 'conversion' THEN 1 ELSE 0 END) as conversions"),
                DB::raw("SUM(CASE WHEN event_type = 'click' THEN 1 ELSE 0 END) as clicks")
            )
            ->whereBetween('occurred_at', [$range['start'], $range['end']])
            ->where('event_type', 'view')
            ->groupBy('popup_id')
            ->orderByDesc('total_views')
            ->limit($limit)
            ->get();
    }

    public function getDeviceBreakdown(string $popupId, string $period = '30d'): Collection
    {
        $range = $this->getDateRange($period);
        return $this->model
            ->select('device_type', DB::raw('COUNT(*) as total'))
            ->where('popup_id', $popupId)
            ->whereBetween('occurred_at', [$range['start'], $range['end']])
            ->groupBy('device_type')
            ->get();
    }

    public function getCountryBreakdown(string $popupId, string $period = '30d'): Collection
    {
        $range = $this->getDateRange($period);
        return $this->model
            ->select('country', DB::raw('COUNT(*) as total'))
            ->where('popup_id', $popupId)
            ->whereNotNull('country')
            ->whereBetween('occurred_at', [$range['start'], $range['end']])
            ->groupBy('country')
            ->orderByDesc('total')
            ->get();
    }

    public function getDashboardStats(): array
    {
        return Cache::remember('popup:dashboard:stats', 300, function () {
            $today = today();
            $thisMonth = now()->startOfMonth();
            return [
                'total_popups' => Popup::count(),
                'active_popups' => Popup::where('status', 'active')->count(),
                'views_today' => $this->model->whereDate('occurred_at', $today)->where('event_type', 'view')->count(),
                'views_month' => $this->model->where('occurred_at', '>=', $thisMonth)->where('event_type', 'view')->count(),
                'conversions_month' => $this->model->where('occurred_at', '>=', $thisMonth)->where('event_type', 'conversion')->count(),
                'total_leads' => PopupLead::count(),
                'new_leads_today' => PopupLead::whereDate('created_at', $today)->count(),
                'top_popup' => $this->getTopPopups(1, '30d')->first(),
            ];
        });
    }

    private function countByEvent(string $popupId, string $eventType, array $range): int
    {
        return $this->model
            ->where('popup_id', $popupId)
            ->where('event_type', $eventType)
            ->whereBetween('occurred_at', [$range['start'], $range['end']])
            ->count();
    }

    private function calculateRate(string $popupId, string $numerator, string $denominator, array $range): float
    {
        $num = $this->countByEvent($popupId, $numerator, $range);
        $den = $this->countByEvent($popupId, $denominator, $range);
        if ($den === 0) return 0;
        return round(($num / $den) * 100, 2);
    }

    private function getDateRange(string $period): array
    {
        return match ($period) {
            '24h' => ['start' => now()->subDay(), 'end' => now()],
            '7d' => ['start' => now()->subWeek(), 'end' => now()],
            '30d' => ['start' => now()->subDays(30), 'end' => now()],
            '90d' => ['start' => now()->subDays(90), 'end' => now()],
            '12m' => ['start' => now()->subYear(), 'end' => now()],
            default => ['start' => now()->subWeek(), 'end' => now()],
        };
    }

    private function clearCache(string $popupId): void
    {
        Cache::forget('popup:dashboard:stats');
        foreach (['24h', '7d', '30d', '90d', '12m'] as $period) {
            Cache::forget("popup:stats:{$popupId}:{$period}");
        }
    }
}
