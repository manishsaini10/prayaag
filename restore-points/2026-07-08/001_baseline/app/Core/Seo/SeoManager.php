<?php

namespace App\Core\Seo;

use App\Core\Settings\SettingsManager;
use App\Models\Page;

/**
 * Resolves a complete, never-empty SeoData object for a page, applying a
 * deterministic fallback chain:
 *
 *   per-page SEO  →  auto-generated from content  →  global SEO settings  →  hard default
 *
 * This is the single source of truth for <head> metadata (Phase 4 + 5). It is
 * reusable: any future content type can call resolve() by passing a title,
 * slug, per-record seo array, and (optionally) rendered HTML for extraction.
 */
class SeoManager
{
    public function __construct(protected SettingsManager $settings)
    {
    }

    /** Resolve SEO for a CMS Page. $html is the rendered body (for extraction). */
    public function forPage(Page $page, ?string $html = null, bool $isHome = false): SeoData
    {
        return $this->resolve(
            title:    $page->title,
            slug:     $isHome ? '' : (string) $page->slug,
            seo:      is_array($page->seo) ? $page->seo : [],
            html:     $html,
            isHome:   $isHome,
            published: $page->status === 'published',
        );
    }

    /**
     * Generic resolver usable by any module.
     *
     * @param array<string, mixed> $seo Per-record SEO overrides.
     */
    public function resolve(
        string $title,
        string $slug = '',
        array $seo = [],
        ?string $html = null,
        bool $isHome = false,
        bool $published = true,
    ): SeoData {
        $s = $this->settings;
        $site    = trim((string) $s->get('site_name', 'Prayaag International School'));
        $tagline = trim((string) $s->get('site_tagline', ''));

        $val = fn (string $k): string => trim((string) ($seo[$k] ?? ''));

        // ---- Title -------------------------------------------------------
        $title = (function () use ($val, $isHome, $s, $site, $tagline, $title) {
            if (($t = $val('title')) !== '') {
                return $t;
            }
            if ($isHome) {
                $d = trim((string) $s->get('seo_default_title', ''));
                return $d !== '' ? $d : ($site . ($tagline !== '' ? ' — ' . $tagline : ''));
            }
            $tpl = (string) $s->get('seo_title_template', '{title} | {site}');
            return trim(str_replace(['{title}', '{site}'], [$title, $site], $tpl));
        })();

        // ---- Description -------------------------------------------------
        $description = $val('description');
        if ($description === '') {
            $description = $this->excerpt($html ?? '', 155);
        }
        if ($description === '') {
            $description = trim((string) $s->get('seo_default_description', ''));
        }
        if ($description === '') {
            $description = $tagline;
        }

        // ---- Keywords ----------------------------------------------------
        $kw = $seo['keywords'] ?? '';
        $keywords = is_array($kw) ? implode(', ', $kw) : trim((string) $kw);
        if ($keywords === '') {
            $keywords = trim((string) $s->get('seo_default_keywords', ''));
        }

        // ---- Canonical ---------------------------------------------------
        $canonical = $val('canonical');
        if ($canonical === '') {
            $canonical = $isHome ? url('/') : url('/' . ltrim($slug, '/'));
        }

        // ---- Robots ------------------------------------------------------
        $robots = $val('robots');
        if ($robots === '') {
            $idx = array_key_exists('robots_index', $seo) ? (bool) $seo['robots_index'] : true;
            $fol = array_key_exists('robots_follow', $seo) ? (bool) $seo['robots_follow'] : true;
            if (! $published) {
                $idx = false;
            }
            // Global noindex toggle overrides per-page settings
            if ((bool) $s->get('seo_robots_noindex', false)) {
                $idx = false;
                $fol = false;
            }
            $robots = ($idx ? 'index' : 'noindex') . ', ' . ($fol ? 'follow' : 'nofollow');
        }

        // ---- Open Graph --------------------------------------------------
        $ogTitle = $val('og_title') ?: $title;
        $ogDesc  = $val('og_description') ?: $description;
        $ogImage = $val('og_image');
        if ($ogImage === '') {
            $ogImage = $this->firstImage($html ?? '');
        }
        if ($ogImage === '') {
            $ogImage = trim((string) $s->get('seo_default_og_image', ''));
        }
        if ($ogImage === '') {
            $ogImage = trim((string) $s->get('site_logo', ''));
        }
        $ogType = $isHome ? 'website' : ($val('og_type') ?: 'article');

        // ---- Twitter -----------------------------------------------------
        $twImage = $val('twitter_image') ?: $ogImage;
        $twCard  = $twImage !== '' ? 'summary_large_image' : 'summary';

        return new SeoData(
            title:              $title,
            description:        $description,
            keywords:           $keywords,
            canonical:          $canonical,
            robots:             $robots,
            ogType:             $ogType,
            ogTitle:            $ogTitle,
            ogDescription:      $ogDesc,
            ogImage:            $ogImage,
            ogUrl:              $canonical,
            siteName:           $site,
            twitterCard:        $twCard,
            twitterTitle:       $val('twitter_title') ?: $ogTitle,
            twitterDescription: $val('twitter_description') ?: $ogDesc,
            twitterImage:       $twImage,
            twitterSite:        trim((string) $s->get('seo_twitter_handle', '')),
            locale:             trim((string) $s->get('seo_locale', 'en_IN')) ?: 'en_IN',
        );
    }

    /** Plain-text excerpt from rendered HTML, trimmed to a word boundary. */
    protected function excerpt(string $html, int $limit = 155): string
    {
        if ($html === '') {
            return '';
        }
        // Drop non-content elements entirely.
        $html = preg_replace('#<(script|style|noscript)[^>]*>.*?</\1>#is', ' ', $html) ?? $html;
        $text = trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        if ($text === '') {
            return '';
        }
        if (mb_strlen($text) <= $limit) {
            return $text;
        }
        $cut = mb_substr($text, 0, $limit);
        $sp = mb_strrpos($cut, ' ');
        if ($sp !== false && $sp > 40) {
            $cut = mb_substr($cut, 0, $sp);
        }
        return rtrim($cut, " ,.;:-") . '…';
    }

    /** First <img src> in the rendered HTML, if any. */
    protected function firstImage(string $html): string
    {
        if ($html !== '' && preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $m)) {
            return trim($m[1]);
        }
        return '';
    }
}
