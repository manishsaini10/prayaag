<?php

namespace App\Core\Seo;

use App\Core\Settings\SettingsManager;
use App\Models\Page;
use Illuminate\Support\Str;

/**
 * Builds a single JSON-LD @graph per page (Phase 6). Nodes are cross-linked by
 * @id so search engines understand the relationships:
 *
 *   Organization (EducationalOrganization) ── publisher of ──▶ WebSite
 *   WebSite ── has page ──▶ WebPage ── has ──▶ BreadcrumbList
 *
 * The WebPage @type is selectable per page (WebPage, AboutPage, ContactPage,
 * CollectionPage, FAQPage, …) via the page's seo[schema_type].
 */
class SchemaBuilder
{
    /** Page-level schema types offered in the SEO editor. */
    public const PAGE_TYPES = [
        'WebPage'        => 'Web Page (default)',
        'AboutPage'      => 'About Page',
        'ContactPage'    => 'Contact Page',
        'CollectionPage' => 'Collection / Listing',
        'FAQPage'        => 'FAQ Page',
        'ProfilePage'    => 'Profile Page',
        'CheckoutPage'   => 'Checkout / Apply',
    ];

    public function __construct(protected SettingsManager $settings)
    {
    }

    /** Full <script type="application/ld+json"> graph for a page. */
    public function forPage(Page $page, SeoData $seo, bool $isHome = false): string
    {
        $url = $isHome ? url('/') : url('/' . ltrim((string) $page->slug, '/'));

        $graph = [
            $this->organization(),
            $this->website(),
            $this->webPage($page, $seo, $url, $isHome),
        ];

        if (! $isHome && ($bc = $this->breadcrumb($page, $url)) !== null) {
            $graph[] = $bc;
        }

        return $this->wrap($graph);
    }

    /** EducationalOrganization / school identity from settings. */
    protected function organization(): array
    {
        $s = $this->settings;
        $url = url('/');
        $type = trim((string) $s->get('seo_schema_org_type', 'EducationalOrganization')) ?: 'EducationalOrganization';

        $sameAs = array_values(array_filter([
            $s->get('social_facebook'), $s->get('social_instagram'), $s->get('social_twitter'),
            $s->get('social_linkedin'), $s->get('social_youtube'),
        ]));

        return array_filter([
            '@type'     => $type,
            '@id'       => $url . '#org',
            'name'      => $s->get('site_name', 'Prayaag International School'),
            'url'       => $url,
            'logo'      => $s->get('site_logo') ?: null,
            'email'     => $s->get('contact_email') ?: null,
            'telephone' => $s->get('contact_phone') ?: null,
            'address'   => ($addr = $s->get('contact_address'))
                ? ['@type' => 'PostalAddress', 'streetAddress' => $addr, 'addressCountry' => 'IN']
                : null,
            'sameAs'    => $sameAs ?: null,
        ], fn ($v) => $v !== null && $v !== '');
    }

    /** WebSite node with an on-site SearchAction (uses the /search route). */
    protected function website(): array
    {
        $url = url('/');

        return [
            '@type'     => 'WebSite',
            '@id'       => $url . '#website',
            'name'      => $this->settings->get('site_name', 'Prayaag International School'),
            'url'       => $url,
            'publisher' => ['@id' => $url . '#org'],
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => ['@type' => 'EntryPoint', 'urlTemplate' => url('/search') . '?q={search_term_string}'],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /** The page itself, typed per the editor selection. */
    protected function webPage(Page $page, SeoData $seo, string $url, bool $isHome): array
    {
        $seoArr = is_array($page->seo) ? $page->seo : [];
        $type = $seoArr['schema_type'] ?? null;
        if (! is_string($type) || ! array_key_exists($type, self::PAGE_TYPES)) {
            $type = $isHome ? 'WebPage' : 'WebPage';
        }

        return array_filter([
            '@type'              => $type,
            '@id'                => $url . '#webpage',
            'url'                => $url,
            'name'               => $seo->title,
            'description'        => $seo->description,
            'isPartOf'           => ['@id' => url('/') . '#website'],
            'about'              => ['@id' => url('/') . '#org'],
            'inLanguage'         => str_replace('_', '-', $seo->locale),
            'primaryImageOfPage' => $seo->ogImage !== '' ? ['@type' => 'ImageObject', 'url' => $seo->ogImage] : null,
            'breadcrumb'         => $isHome ? null : ['@id' => $url . '#breadcrumb'],
            'datePublished'      => optional($page->created_at)->toIso8601String(),
            'dateModified'       => optional($page->updated_at)->toIso8601String(),
        ], fn ($v) => $v !== null && $v !== '');
    }

    /** Home › <Page Title> breadcrumb trail. */
    protected function breadcrumb(Page $page, string $url): ?array
    {
        $items = [[
            '@type'    => 'ListItem',
            'position' => 1,
            'name'     => 'Home',
            'item'     => url('/'),
        ], [
            '@type'    => 'ListItem',
            'position' => 2,
            'name'     => $page->title ?: Str::headline((string) $page->slug),
            'item'     => $url,
        ]];

        return [
            '@type'           => 'BreadcrumbList',
            '@id'             => $url . '#breadcrumb',
            'itemListElement' => $items,
        ];
    }

    protected function wrap(array $graph): string
    {
        $doc = ['@context' => 'https://schema.org', '@graph' => array_values($graph)];

        return '<script type="application/ld+json">'
            . json_encode($doc, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            . '</script>';
    }
}
