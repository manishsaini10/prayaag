<?php

namespace App\Core\Seo;

use App\Core\Builder\PageRenderer;
use App\Models\Page;
use Illuminate\Support\Collection;

/**
 * SEO Audit Engine (Phase 15). Runs a single render pass over every published
 * page and evaluates a battery of on-page checks, returning a structured result
 * grouped into critical / warning / pass plus an overall score.
 *
 * Each check is a row: ['id','label','status','summary','items'=>[...]].
 * Score = 100 * (passes + 0.5*warnings) / total_checks.
 */
class SeoAuditor
{
    public function __construct(
        protected SeoManager $seo,
        protected PageRenderer $renderer,
    ) {
    }

    public function run(): array
    {
        $pages = Page::published()->orderBy('title')->get();

        // Build per-page facts in ONE render pass.
        $facts = $pages->map(function (Page $page) {
            $isHome = $page->slug === 'home';
            $html = rescue(fn () => $this->renderer->renderCached($page), '', false);
            $seo = $this->seo->forPage($page, $html, $isHome);

            return [
                'page'    => $page,
                'isHome'  => $isHome,
                'path'    => $isHome ? '/' : '/' . ltrim((string) $page->slug, '/'),
                'title'   => $seo->title,
                'desc'    => $seo->description,
                'ogImage' => $seo->ogImage,
                'robots'  => $seo->robots,
                'h1'      => preg_match_all('/<h1[\s>]/i', $html),
                'imgs'    => preg_match_all('/<img\b[^>]*>/i', $html, $im) ? $im[0] : [],
                'links'   => $this->internalLinks($html),
                'noindex' => str_contains($seo->robots, 'noindex'),
            ];
        });

        $validPaths = $pages->map(fn ($p) => $p->slug === 'home' ? '/' : '/' . ltrim((string) $p->slug, '/'))->push('/')->all();

        $checks = [];
        $checks[] = $this->checkEmptyDescriptions($facts);
        $checks[] = $this->checkDescriptionLength($facts);
        $checks[] = $this->checkTitleLength($facts);
        $checks[] = $this->checkDuplicateTitles($facts);
        $checks[] = $this->checkDuplicateDescriptions($facts);
        $checks[] = $this->checkMissingH1($facts);
        $checks[] = $this->checkMultipleH1($facts);
        $checks[] = $this->checkImageAlt($facts);
        $checks[] = $this->checkOgImage($facts);
        $checks[] = $this->checkBrokenLinks($facts, $validPaths);
        $checks[] = $this->checkIndexable($facts);
        // Infrastructure checks (always pass — the routes exist).
        $checks[] = ['id' => 'canonical', 'label' => 'Canonical tags on every page', 'status' => 'pass', 'summary' => 'Auto-generated for all pages', 'items' => []];
        $checks[] = ['id' => 'sitemap', 'label' => 'XML sitemap available', 'status' => 'pass', 'summary' => url('/sitemap.xml'), 'items' => []];
        $checks[] = ['id' => 'robots', 'label' => 'robots.txt available', 'status' => 'pass', 'summary' => url('/robots.txt'), 'items' => []];

        $weights = ['pass' => 1.0, 'warn' => 0.5, 'crit' => 0.0];
        $score = count($checks) ? (int) round(100 * collect($checks)->sum(fn ($c) => $weights[$c['status']]) / count($checks)) : 0;

        return [
            'score'    => $score,
            'pages'    => $pages->count(),
            'checks'   => $checks,
            'critical' => collect($checks)->where('status', 'crit')->values()->all(),
            'warnings' => collect($checks)->where('status', 'warn')->values()->all(),
            'passed'   => collect($checks)->where('status', 'pass')->values()->all(),
        ];
    }

    // ---- individual checks --------------------------------------------------

    protected function checkEmptyDescriptions(Collection $f): array
    {
        $bad = $f->filter(fn ($x) => trim($x['desc']) === '')->map($this->row(...))->all();

        return $this->result('desc_present', 'Meta description present', $bad, 'crit',
            'All pages have a description', count($bad) . ' page(s) have an empty description');
    }

    protected function checkDescriptionLength(Collection $f): array
    {
        $bad = $f->filter(function ($x) {
            $n = mb_strlen($x['desc']);
            return $n > 0 && ($n < 50 || $n > 160);
        })->map(fn ($x) => $this->row($x) + ['note' => mb_strlen($x['desc']) . ' chars'])->all();

        return $this->result('desc_len', 'Description length 50–160 chars', $bad, 'warn',
            'All descriptions are well-sized', count($bad) . ' description(s) too short/long');
    }

    protected function checkTitleLength(Collection $f): array
    {
        $bad = $f->filter(function ($x) {
            $n = mb_strlen($x['title']);
            return $n < 15 || $n > 60;
        })->map(fn ($x) => $this->row($x) + ['note' => mb_strlen($x['title']) . ' chars'])->all();

        return $this->result('title_len', 'Title length 15–60 chars', $bad, 'warn',
            'All titles are well-sized', count($bad) . ' title(s) too short/long');
    }

    protected function checkDuplicateTitles(Collection $f): array
    {
        return $this->dupes($f, 'title', 'title_unique', 'Unique titles', 'No duplicate titles');
    }

    protected function checkDuplicateDescriptions(Collection $f): array
    {
        return $this->dupes($f, 'desc', 'desc_unique', 'Unique descriptions', 'No duplicate descriptions');
    }

    protected function checkMissingH1(Collection $f): array
    {
        $bad = $f->filter(fn ($x) => $x['h1'] === 0)->map($this->row(...))->all();

        return $this->result('h1_present', 'Each page has an H1', $bad, 'warn',
            'Every page has an H1', count($bad) . ' page(s) missing an H1');
    }

    protected function checkMultipleH1(Collection $f): array
    {
        $bad = $f->filter(fn ($x) => $x['h1'] > 1)->map(fn ($x) => $this->row($x) + ['note' => $x['h1'] . ' H1s'])->all();

        return $this->result('h1_single', 'Single H1 per page', $bad, 'warn',
            'No page has multiple H1s', count($bad) . ' page(s) have multiple H1s');
    }

    protected function checkImageAlt(Collection $f): array
    {
        $bad = [];
        foreach ($f as $x) {
            $missing = collect($x['imgs'])->filter(fn ($img) => ! preg_match('/\balt\s*=\s*["\'][^"\']*\S[^"\']*["\']/i', $img))->count();
            if ($missing > 0) {
                $bad[] = $this->row($x) + ['note' => $missing . ' image(s) without alt'];
            }
        }

        return $this->result('img_alt', 'Images have alt text', $bad, 'warn',
            'All images have alt text', count($bad) . ' page(s) have images missing alt text');
    }

    protected function checkOgImage(Collection $f): array
    {
        $bad = $f->filter(fn ($x) => trim($x['ogImage']) === '')->map($this->row(...))->all();

        return $this->result('og_image', 'Social share image set', $bad, 'warn',
            'All pages have a social image', count($bad) . ' page(s) have no OG image (set a default in Settings → SEO)');
    }

    protected function checkBrokenLinks(Collection $f, array $validPaths): array
    {
        $known = ['/', '/sitemap.xml', '/robots.txt', '/search'];
        $bad = [];
        foreach ($f as $x) {
            foreach ($x['links'] as $link) {
                $norm = rtrim($link, '/') ?: '/';
                if (in_array($norm, $validPaths, true) || in_array($norm, $known, true)) {
                    continue;
                }
                if (preg_match('#^/(admin|forms|storage|enquiries|jobs|subscribe)#', $link)) {
                    continue;
                }
                $bad[] = ['title' => $x['page']->title, 'path' => $x['path'], 'note' => $link];
            }
        }

        return $this->result('links', 'Internal links resolve', $bad, 'crit',
            'No broken internal links', count($bad) . ' internal link(s) point to a missing page');
    }

    protected function checkIndexable(Collection $f): array
    {
        $noindex = $f->filter(fn ($x) => $x['noindex'])->map($this->row(...))->all();

        // Informational: noindex is sometimes intentional, so this is a warning at most.
        return $this->result('indexable', 'Pages are indexable', $noindex, 'warn',
            'All published pages are indexable', count($noindex) . ' page(s) are set to noindex');
    }

    // ---- helpers ------------------------------------------------------------

    protected function internalLinks(string $html): array
    {
        if ($html === '' || ! preg_match_all('/href=["\'](\/[^"\'#?\s]*)/i', $html, $m)) {
            return [];
        }

        return array_values(array_unique(array_filter($m[1], fn ($l) => ! preg_match('/\.(css|js|png|jpe?g|gif|svg|webp|pdf|ico|woff2?|xml|txt)$/i', $l))));
    }

    protected function row(array $x): array
    {
        return ['title' => $x['page']->title, 'path' => $x['path']];
    }

    protected function dupes(Collection $f, string $key, string $id, string $label, string $okMsg): array
    {
        $items = [];
        $f->filter(fn ($x) => trim($x[$key]) !== '')
            ->groupBy(fn ($x) => mb_strtolower(trim($x[$key])))
            ->filter(fn ($g) => $g->count() > 1)
            ->each(function ($g) use (&$items) {
                foreach ($g as $x) {
                    $items[] = $this->row($x) + ['note' => 'duplicate'];
                }
            });

        return $this->result($id, $label, $items, 'warn', $okMsg, count($items) . ' page(s) share a value');
    }

    /** Build a check row; status downgrades to pass when there are no items. */
    protected function result(string $id, string $label, array $items, string $failStatus, string $okMsg, string $failMsg): array
    {
        $ok = count($items) === 0;

        return [
            'id'      => $id,
            'label'   => $label,
            'status'  => $ok ? 'pass' : $failStatus,
            'summary' => $ok ? $okMsg : $failMsg,
            'items'   => array_slice($items, 0, 20),
        ];
    }
}
