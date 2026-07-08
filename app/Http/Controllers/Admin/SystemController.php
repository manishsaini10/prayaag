<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Core\Builder\PageRenderer;
use App\Core\Seo\SeoManager;
use App\Core\Theme\ThemeRenderer;
use App\Http\Requests\Admin\PageSeoRequest;
use App\Models\ActivityLog;
use App\Models\NotFoundLog;
use App\Models\Page;
use App\Models\Redirect;
use App\Models\Setting;
use App\Models\SettingGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Dedicated screens for the modules that aren't simple CRUD: Settings (grouped
 * key/value editor), SEO coverage, the live System Health panel, and the
 * Notifications stream (sourced from the activity log).
 */
class SystemController extends Controller
{
    public function settings(): View
    {
        $groups = SettingGroup::orderBy('sort_order')->orderBy('name')->get();
        $settings = Setting::orderBy('key')->get();
        $grouped = $settings->groupBy('group_id');

        return view('admin.system.settings', compact('groups', 'settings', 'grouped'));
    }

    public function saveSettings(Request $request): RedirectResponse
    {
        $values = $request->input('settings', []);

        foreach (Setting::all() as $setting) {
            if (! array_key_exists($setting->id, $values)) {
                // Unchecked booleans don't post; treat their absence as false.
                if ($setting->type === 'boolean') {
                    $setting->update(['value' => '0']);
                }
                continue;
            }

            $value = $values[$setting->id];
            if ($setting->type === 'boolean') {
                $value = $value ? '1' : '0';
            }

            $setting->update(['value' => is_array($value) ? json_encode($value) : (string) $value]);
        }

        // Settings drive the cached header/footer chrome and the settings cache.
        // Flush them so saved changes (header/footer variant, colors, fonts,
        // toggles) take effect immediately — no manual cache:clear needed.
        ThemeRenderer::flush();
        rescue(fn () => Cache::forget('settings.all'), null, false);
        rescue(fn () => Cache::flush(), null, false);

        return back()->with('status', 'Settings saved.');
    }

    public function seo(Request $request, PageRenderer $renderer): View
    {
        $pages = Page::orderBy('title')->get(['id', 'title', 'slug', 'status', 'seo']);

        $rows = $pages->map(fn (Page $p) => [
            'id'     => $p->id,
            'title'  => $p->title,
            'slug'   => $p->slug,
            'status' => $p->status,
            'hasTitle' => filled($p->seo['title'] ?? null),
            'hasDesc'  => filled($p->seo['description'] ?? null),
        ]);

        $published = $pages->where('status', 'published')->count();
        $publishedRows = $rows->where('status', 'published');
        $withTitle = $publishedRows->where('hasTitle', true)->count();
        $withDesc  = $publishedRows->where('hasDesc', true)->count();

        $score = $published > 0 ? (int) round((($withTitle + $withDesc) / ($published * 2)) * 100) : 0;
        $redirectsActive = rescue(fn () => Redirect::where('is_active', true)->count(), 0, false);

        // On-demand broken internal link scan (slow-ish, so only when asked).
        $broken = $request->boolean('scan') ? $this->scanLinks($renderer) : null;

        return view('admin.system.seo', [
            'rows'            => $rows,
            'published'       => $published,
            'missing'         => $published - $withDesc,
            'covered'         => $withDesc,
            'withTitle'       => $withTitle,
            'score'           => $score,
            'redirectsActive' => $redirectsActive,
            'sitemapUrls'     => $published,
            'broken'          => $broken,
        ]);
    }

    /**
     * Render every published page and flag internal links that don't resolve
     * to a published page or a known public route.
     *
     * @return array<int, array{page: string, slug: string, link: string}>
     */
    protected function scanLinks(PageRenderer $renderer): array
    {
        $published = Page::published()->get(['id', 'title', 'slug']);
        $validPaths = $published->map(fn ($p) => '/' . ltrim($p->slug, '/'))->push('/')->all();
        $known = ['/', '/sitemap.xml', '/robots.txt'];

        $broken = [];
        foreach ($published as $p) {
            $html = rescue(fn () => $renderer->render($p), '', false);
            if (! preg_match_all('/href=["\'](\/[^"\'#?\s]*)/i', $html, $m)) {
                continue;
            }
            foreach (array_unique($m[1]) as $link) {
                $norm = rtrim($link, '/') ?: '/';
                if (in_array($norm, $validPaths, true) || in_array($norm, $known, true)) {
                    continue;
                }
                if (str_starts_with($link, '/admin') || str_starts_with($link, '/forms') || str_starts_with($link, '/storage')) {
                    continue;
                }
                if (preg_match('/\.(css|js|png|jpe?g|gif|svg|webp|pdf|ico|woff2?|xml|txt)$/i', $link)) {
                    continue;
                }
                $broken[] = ['page' => $p->title, 'slug' => $p->slug, 'link' => $link];
            }
        }

        return $broken;
    }

    /** Per-page SEO editor: shows auto-generated values + lets you override. */
    public function editSeo(Page $page, PageRenderer $renderer, SeoManager $seo): View
    {
        $isHome = $page->slug === 'home';
        $html = rescue(fn () => $renderer->render($page), '', false);

        // What the meta WOULD be with no overrides (for placeholders/preview).
        $auto = $seo->resolve(
            title: $page->title,
            slug: $isHome ? '' : (string) $page->slug,
            seo: [],
            html: $html,
            isHome: $isHome,
            published: $page->status === 'published',
        )->toArray();

        return view('admin.system.seo-edit', [
            'page'        => $page,
            'current'     => is_array($page->seo) ? $page->seo : [],
            'auto'        => $auto,
            'schemaTypes' => \App\Core\Seo\SchemaBuilder::PAGE_TYPES,
        ]);
    }

    /** Persist per-page SEO overrides (empty fields fall back to auto-gen). */
    public function updateSeo(PageSeoRequest $request, Page $page): RedirectResponse
    {
        $page->update(['seo' => $request->seoData()]);

        return redirect()->route('admin.seo')->with('status', 'SEO updated for “' . $page->title . '”.');
    }

    /** Full on-page SEO audit (Phase 15): runs every check + overall score. */
    public function audit(\App\Core\Seo\SeoAuditor $auditor): View
    {
        $report = rescue(fn () => $auditor->run(), ['score' => 0, 'pages' => 0, 'checks' => [], 'critical' => [], 'warnings' => [], 'passed' => []], false);

        return view('admin.system.seo-audit', ['report' => $report]);
    }

    /** Bulk SEO editor (Phase 21): edit title/description/indexing for all pages. */
    public function seoBulk(SeoManager $seo): View
    {
        $rows = Page::orderBy('title')->get()->map(function (Page $page) {
            $s = is_array($page->seo) ? $page->seo : [];
            return [
                'id'          => $page->id,
                'title'       => $page->title,
                'slug'        => $page->slug,
                'status'      => $page->status,
                'metaTitle'   => $s['title'] ?? '',
                'metaDesc'    => $s['description'] ?? '',
                'autoTitle'   => trim((string) $page->title),
                'noindex'     => array_key_exists('robots_index', $s) && $s['robots_index'] === false,
            ];
        });

        return view('admin.system.seo-bulk', ['rows' => $rows]);
    }

    /** Persist bulk SEO edits, merging into each page's existing seo payload. */
    public function seoBulkSave(Request $request): RedirectResponse
    {
        $pages = (array) $request->input('pages', []);
        $changed = 0;

        foreach ($pages as $id => $data) {
            $page = Page::find($id);
            if (! $page) {
                continue;
            }

            $seo = is_array($page->seo) ? $page->seo : [];
            $title = trim((string) ($data['title'] ?? ''));
            $desc  = trim((string) ($data['description'] ?? ''));

            // Empty = remove override (falls back to auto-generation).
            $title === '' ? Arr::forget($seo, 'title') : $seo['title'] = $title;
            $desc === '' ? Arr::forget($seo, 'description') : $seo['description'] = $desc;
            $seo['robots_index'] = ! (bool) ($data['noindex'] ?? false);

            $page->update(['seo' => $seo]);
            $changed++;
        }

        return back()->with('status', $changed . ' page(s) updated.');
    }

    /** 404 monitor: the missing URLs visitors/search engines have hit. */
    public function notFound(): View
    {
        $logs = rescue(fn () => NotFoundLog::unresolved()->orderByDesc('hits')->orderByDesc('last_seen_at')->limit(200)->get(), collect(), false);
        $pages = Page::published()->orderBy('title')->get(['title', 'slug']);

        return view('admin.system.not-found', [
            'logs'        => $logs,
            'pages'       => $pages,
            'totalHits'   => (int) rescue(fn () => NotFoundLog::unresolved()->sum('hits'), 0, false),
            'resolvedNum' => (int) rescue(fn () => NotFoundLog::where('resolved', true)->count(), 0, false),
        ]);
    }

    /** Turn a logged 404 into an active 301 redirect, then resolve it. */
    public function notFoundRedirect(Request $request, NotFoundLog $log): RedirectResponse
    {
        $data = $request->validate([
            'to_path'     => ['required', 'string', 'max:2048'],
            'status_code' => ['nullable', 'in:301,302'],
        ]);

        $from = '/' . ltrim($log->path, '/');
        Redirect::updateOrCreate(
            ['from_path' => $from],
            ['to_path' => $data['to_path'], 'status_code' => (int) ($data['status_code'] ?? 301), 'is_active' => true]
        );
        $log->update(['resolved' => true]);

        return back()->with('status', 'Redirect created: ' . $from . ' → ' . $data['to_path']);
    }

    /** Dismiss a 404 from the report (e.g. spam/bot probe). */
    public function notFoundIgnore(NotFoundLog $log): RedirectResponse
    {
        $log->update(['resolved' => true]);

        return back()->with('status', 'Dismissed ' . $log->path . ' from the 404 report.');
    }

    public function notifications(): View
    {
        $items = ActivityLog::latest()->limit(50)->get(['id', 'log_name', 'description', 'created_at']);

        return view('admin.system.notifications', compact('items'));
    }

    public function health(): View
    {
        return view('admin.system.health', ['health' => $this->probes()]);
    }

    /** @return array<int, array{label: string, value: string, status: string}> */
    protected function probes(): array
    {
        $probe = function (string $label, \Closure $check): array {
            try {
                [$value, $status] = $check();
            } catch (\Throwable $e) {
                [$value, $status] = ['unavailable', 'down'];
            }

            return ['label' => $label, 'value' => $value, 'status' => $status];
        };

        $bytes = function (int|float $b): string {
            $u = ['B', 'KB', 'MB', 'GB', 'TB'];
            $i = 0;
            while ($b >= 1024 && $i < count($u) - 1) {
                $b /= 1024;
                $i++;
            }

            return round($b, 1) . ' ' . $u[$i];
        };

        return [
            $probe('Database', function () {
                DB::select('select 1');

                return [ucfirst(DB::connection()->getDriverName()) . ' · connected', 'ok'];
            }),
            $probe('Cache', function () {
                $k = 'health:' . uniqid();
                Cache::put($k, 1, 5);
                $ok = Cache::get($k) === 1;
                Cache::forget($k);

                return [config('cache.default') . ($ok ? ' · ok' : ' · failed'), $ok ? 'ok' : 'warn'];
            }),
            $probe('Queue', function () {
                $driver = config('queue.default');
                $pending = $driver === 'database' ? (int) DB::table('jobs')->count() : 0;

                return [$driver . ' · ' . $pending . ' pending', $pending > 100 ? 'warn' : 'ok'];
            }),
            $probe('Storage', function () use ($bytes) {
                $free = @disk_free_space(storage_path());
                $total = @disk_total_space(storage_path());
                if (! $free || ! $total) {
                    return ['unavailable', 'warn'];
                }
                $used = (int) round((1 - $free / $total) * 100);

                return [$used . '% used · ' . $bytes($free) . ' free', $used > 90 ? 'warn' : 'ok'];
            }),
            $probe('Mail', fn () => [config('mail.default') . ' · configured', 'ok']),
            $probe('Memory', fn () => [$bytes(memory_get_peak_usage(true)) . ' peak', 'ok']),
            $probe('Runtime', fn () => ['PHP ' . PHP_VERSION . ' · Laravel ' . app()->version(), 'ok']),
            $probe('Security', function () {
                $debug = (bool) config('app.debug');

                return $debug && app()->environment('production')
                    ? ['APP_DEBUG on in production', 'warn']
                    : [app()->environment() . ' · debug ' . ($debug ? 'on' : 'off'), 'ok'];
            }),
        ];
    }
}
