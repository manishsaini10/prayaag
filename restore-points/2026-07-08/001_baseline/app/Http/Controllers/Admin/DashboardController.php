<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Enquiry;
use App\Models\Event;
use App\Models\JobApplication;
use App\Models\Media;
use App\Models\Notice;
use App\Models\Page;
use App\Models\PageView;
use App\Models\Post;
use App\Models\Subscriber;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Executive dashboard. Aggregates real, first-party metrics from every content
 * and engagement module: KPI cards (total + 30-day delta + 14-day sparkline),
 * trend charts, the activity feed, builder/media/SEO overviews, and live system
 * health. Heavy aggregates are cached briefly; health checks always run live.
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        $metrics = Cache::remember('admin.dashboard.v2', now()->addMinutes(5), fn () => $this->buildMetrics());

        return view('admin.dashboard', $metrics + ['health' => $this->systemHealth()]);
    }

    /** @return array<string, mixed> */
    protected function buildMetrics(): array
    {
        return [
            'kpis'     => $this->kpis(),
            'charts'   => $this->charts(),
            'activity' => $this->activity(),
            'builder'  => $this->builderOverview(),
            'media'    => $this->mediaOverview(),
            'seo'      => $this->seoOverview(),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    protected function kpis(): array
    {
        return [
            $this->metric('Pages', 'document', fn () => Page::query(), 'created_at', url('/admin/pages')),
            $this->metric('Posts', 'pencil', fn () => Post::query()),
            $this->metric('Media', 'photo', fn () => Media::query()),
            $this->metric('Admissions', 'academic-cap', fn () => Enquiry::where('type', 'admission'), 'created_at', url('/admin/enquiries')),
            $this->metric('Applications', 'briefcase', fn () => JobApplication::query(), 'created_at', url('/admin/applications')),
            $this->metric('Enquiries', 'inbox', fn () => Enquiry::query(), 'created_at', url('/admin/enquiries')),
            $this->metric('Subscribers', 'envelope', fn () => Subscriber::query(), 'created_at', url('/admin/subscribers')),
            $this->metric('Events', 'calendar', fn () => Event::query()),
            $this->metric('Notices', 'megaphone', fn () => Notice::query()),
            $this->metric('Users', 'users', fn () => User::query()),
        ];
    }

    /**
     * One KPI: lifetime total, current-vs-previous 30-day window, % delta,
     * trend direction, and a 14-day daily sparkline. `$base` returns a fresh
     * query each call so the windows don't mutate one another.
     */
    protected function metric(string $label, string $icon, Closure $base, string $dateCol = 'created_at', ?string $href = null): array
    {
        $total = (clone $base())->count();
        $cur   = (clone $base())->where($dateCol, '>=', now()->subDays(30))->count();
        $prev  = (clone $base())->whereBetween($dateCol, [now()->subDays(60), now()->subDays(30)])->count();

        $delta = $prev > 0
            ? round((($cur - $prev) / $prev) * 100, 1)
            : ($cur > 0 ? 100.0 : 0.0);

        return [
            'label' => $label,
            'icon'  => $icon,
            'total' => $total,
            'delta' => $delta,
            'trend' => $delta > 0 ? 'up' : ($delta < 0 ? 'down' : 'flat'),
            'spark' => $this->series($base(), $dateCol, 14),
            'href'  => $href,
        ];
    }

    /**
     * Daily counts for the last $days days, zero-filled and in order.
     *
     * @return array<int, int>
     */
    protected function series(Builder $query, string $dateCol, int $days): array
    {
        $start = now()->subDays($days - 1)->startOfDay();

        $rows = $query->where($dateCol, '>=', $start)
            ->selectRaw("date({$dateCol}) as d, count(*) as c")
            ->groupBy('d')
            ->pluck('c', 'd')
            ->all();

        $out = [];
        for ($i = 0; $i < $days; $i++) {
            $day = now()->subDays($days - 1 - $i)->toDateString();
            $out[] = (int) ($rows[$day] ?? 0);
        }

        return $out;
    }

    /**
     * 30-day trend lines for the modules that actually capture time-series data.
     * SEO performance is intentionally omitted — there is no metrics source for
     * it yet, and fabricating a line would violate "no demo data".
     *
     * @return array<string, array{labels: array<int, string>, data: array<int, int>}>
     */
    protected function charts(): array
    {
        $labels = [];
        for ($i = 29; $i >= 0; $i--) {
            $labels[] = now()->subDays($i)->format('M j');
        }

        $line = fn (Builder $q, string $col) => [
            'labels' => $labels,
            'data'   => $this->series($q, $col, 30),
        ];

        return [
            'admissions'   => $line(Enquiry::where('type', 'admission'), 'created_at'),
            'applications' => $line(JobApplication::query(), 'created_at'),
            'enquiries'    => $line(Enquiry::query(), 'created_at'),
            'visitors'     => $line(PageView::query(), 'viewed_at'),
            'content'      => $line(Post::whereNotNull('published_at'), 'published_at'),
        ];
    }

    /** Latest audit-trail entries for the activity feed. */
    protected function activity(): Collection
    {
        return ActivityLog::query()
            ->latest()
            ->limit(12)
            ->get(['id', 'log_name', 'description', 'causer_type', 'causer_id', 'created_at']);
    }

    /** @return array<string, mixed> */
    protected function builderOverview(): array
    {
        return [
            'published' => Page::where('status', 'published')->count(),
            'drafts'    => Page::where('status', 'draft')->count(),
            'recent'    => Page::latest('updated_at')->limit(5)->get(['id', 'title', 'slug', 'status', 'updated_at']),
        ];
    }

    /** @return array<string, mixed> */
    protected function mediaOverview(): array
    {
        return [
            'images' => Media::where('mime_type', 'like', 'image/%')->count(),
            'docs'   => Media::where('mime_type', 'not like', 'image/%')->count(),
            'bytes'  => (int) Media::sum('size'),
            'recent' => Media::latest()->limit(6)->get(['id', 'original_name', 'mime_type', 'size', 'disk', 'path']),
        ];
    }

    /**
     * SEO coverage from real page data: how many published pages are missing a
     * meta description. Computed in PHP (the seo column is JSON; the published
     * set is small) to stay portable across SQLite/MySQL.
     *
     * @return array<string, int>
     */
    protected function seoOverview(): array
    {
        $published = Page::published()->get(['id', 'title', 'slug', 'seo']);

        $missing = $published->filter(fn (Page $p) => blank($p->seo['description'] ?? null))->count();

        return [
            'published'   => $published->count(),
            'missingDesc' => $missing,
            'covered'     => $published->count() - $missing,
        ];
    }

    /**
     * Live infrastructure health. Every probe is wrapped so a failing check
     * degrades to a "down"/"warn" badge rather than breaking the dashboard.
     *
     * @return array<int, array{label: string, value: string, status: string}>
     */
    protected function systemHealth(): array
    {
        return [
            $this->probe('Database', function () {
                DB::connection()->getPdo();
                DB::select('select 1');

                return [ucfirst(DB::connection()->getDriverName()) . ' · connected', 'ok'];
            }),
            $this->probe('Cache', function () {
                $key = 'health:' . uniqid();
                Cache::put($key, 1, 5);
                $ok = Cache::get($key) === 1;
                Cache::forget($key);

                return [config('cache.default') . ($ok ? ' · ok' : ' · read failed'), $ok ? 'ok' : 'warn'];
            }),
            $this->probe('Queue', function () {
                $driver = config('queue.default');
                $pending = $driver === 'database' ? (int) DB::table('jobs')->count() : 0;

                return [$driver . ' · ' . $pending . ' pending', $pending > 100 ? 'warn' : 'ok'];
            }),
            $this->probe('Storage', function () {
                $free = @disk_free_space(storage_path());
                $total = @disk_total_space(storage_path());
                if (! $free || ! $total) {
                    return ['unavailable', 'warn'];
                }
                $usedPct = (int) round((1 - $free / $total) * 100);

                return [$usedPct . '% used · ' . $this->humanBytes($free) . ' free', $usedPct > 90 ? 'warn' : 'ok'];
            }),
            $this->probe('Mail', fn () => [config('mail.default') . ' · configured', 'ok']),
            $this->probe('Memory', fn () => [$this->humanBytes(memory_get_peak_usage(true)) . ' peak', 'ok']),
            $this->probe('Runtime', fn () => ['PHP ' . PHP_VERSION . ' · Laravel ' . app()->version(), 'ok']),
            $this->probe('Security', function () {
                $debug = (bool) config('app.debug');
                $prod = app()->environment('production');

                return $debug && $prod
                    ? ['APP_DEBUG on in production', 'warn']
                    : [app()->environment() . ' · debug ' . ($debug ? 'on' : 'off'), 'ok'];
            }),
        ];
    }

    /** @return array{label: string, value: string, status: string} */
    protected function probe(string $label, Closure $check): array
    {
        try {
            [$value, $status] = $check();
        } catch (\Throwable $e) {
            [$value, $status] = ['unavailable', 'down'];
        }

        return ['label' => $label, 'value' => $value, 'status' => $status];
    }

    protected function humanBytes(int|float $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 1) . ' ' . $units[$i];
    }
}
