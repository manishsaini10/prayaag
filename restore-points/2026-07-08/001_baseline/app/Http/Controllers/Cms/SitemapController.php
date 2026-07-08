<?php

namespace App\Http\Controllers\Cms;

use App\Core\Builder\PageRenderer;
use App\Core\Settings\SettingsManager;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

/**
 * SEO discovery surface, all generated live from the database:
 *
 *   /sitemap.xml         → sitemap index (links to the sub-sitemaps)
 *   /sitemap-pages.xml   → published pages (lastmod, changefreq, priority)
 *   /sitemap-images.xml  → images found on each published page
 *   /robots.txt          → crawl rules + sitemap reference (+ custom rules)
 *   /{key}.txt           → IndexNow key verification file
 *
 * Pages flagged noindex (seo.robots_index = false) are excluded from sitemaps.
 */
class SitemapController extends Controller
{
    /** Sitemap index pointing at the sub-sitemaps. */
    public function sitemap(): Response
    {
        $now = now()->toAtomString();
        $maps = [url('/sitemap-pages.xml'), url('/sitemap-images.xml')];

        $body = '';
        foreach ($maps as $loc) {
            $body .= '<sitemap><loc>' . htmlspecialchars($loc, ENT_XML1) . '</loc>'
                . '<lastmod>' . $now . '</lastmod></sitemap>';
        }

        return $this->xml('<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . $body . '</sitemapindex>');
    }

    /** Published, indexable pages. */
    public function pages(): Response
    {
        $urls = '';
        foreach ($this->indexablePages() as $page) {
            $isHome = $page->slug === 'home';
            $loc = htmlspecialchars(url($isHome ? '/' : '/' . ltrim($page->slug, '/')), ENT_XML1);

            $urls .= '<url>'
                . '<loc>' . $loc . '</loc>'
                . (optional($page->updated_at) ? '<lastmod>' . $page->updated_at->toAtomString() . '</lastmod>' : '')
                . '<changefreq>' . ($isHome ? 'daily' : 'weekly') . '</changefreq>'
                . '<priority>' . ($isHome ? '1.0' : '0.8') . '</priority>'
                . '</url>';
        }

        return $this->xml('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . $urls . '</urlset>');
    }

    /** Image sitemap: every <img> found on each indexable page. */
    public function images(PageRenderer $renderer): Response
    {
        $xml = Cache::remember('sitemap.images', now()->addHours(6), function () use ($renderer) {
            $urls = '';
            foreach ($this->indexablePages() as $page) {
                $isHome = $page->slug === 'home';
                $loc = htmlspecialchars(url($isHome ? '/' : '/' . ltrim($page->slug, '/')), ENT_XML1);

                $html = rescue(fn () => $renderer->renderCached($page), '', false);
                preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $m);
                $imgs = array_values(array_unique(array_filter($m[1] ?? [], fn ($u) => str_starts_with($u, 'http'))));
                if (! $imgs) {
                    continue;
                }

                $entries = '';
                foreach (array_slice($imgs, 0, 1000) as $img) {
                    $entries .= '<image:image><image:loc>' . htmlspecialchars($img, ENT_XML1) . '</image:loc></image:image>';
                }
                $urls .= '<url><loc>' . $loc . '</loc>' . $entries . '</url>';
            }

            return '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" '
                . 'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . $urls . '</urlset>';
        });

        return $this->xml($xml);
    }

    public function robots(SettingsManager $settings): Response
    {
        $noindex = (bool) $settings->get('seo_robots_noindex', false);

        if ($noindex) {
            $body = "User-agent: *\nDisallow: /\n";
        } else {
            $custom = trim((string) $settings->get('seo_robots_custom', ''));

            if ($custom !== '') {
                $body = $custom;
            } else {
                $body = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /login\nDisallow: /forms/\n";
            }
        }

        // Always advertise the sitemap index (append if the admin forgot).
        if (! str_contains(strtolower($body), 'sitemap:')) {
            $body = rtrim($body) . "\nSitemap: " . url('/sitemap.xml') . "\n";
        }

        return response($body, 200, ['Content-Type' => 'text/plain']);
    }

    /** IndexNow ownership verification file: /{key}.txt returns the key. */
    public function indexNowKey(string $key, SettingsManager $settings): Response
    {
        $configured = trim((string) $settings->get('seo_indexnow_key', ''));
        abort_unless($configured !== '' && hash_equals($configured, $key), 404);

        return response($configured, 200, ['Content-Type' => 'text/plain']);
    }

    /** @return \Illuminate\Support\Collection<int, Page> */
    protected function indexablePages()
    {
        $noindex = (bool) app(SettingsManager::class)->get('seo_robots_noindex', false);

        if ($noindex) {
            return collect();
        }

        return Page::published()->orderByDesc('updated_at')->get()
            ->filter(fn (Page $p) => ! (is_array($p->seo) && array_key_exists('robots_index', $p->seo) && $p->seo['robots_index'] === false));
    }

    protected function xml(string $body): Response
    {
        return response('<?xml version="1.0" encoding="UTF-8"?>' . $body, 200, ['Content-Type' => 'application/xml']);
    }
}
