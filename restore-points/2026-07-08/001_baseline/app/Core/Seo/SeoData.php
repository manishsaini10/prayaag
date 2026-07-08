<?php

namespace App\Core\Seo;

/**
 * Immutable, fully-resolved SEO metadata for a single request. Every field is
 * guaranteed non-empty by SeoManager (no field is ever blank in the <head>).
 * Produced by SeoManager and consumed by the seo-head Blade partial.
 */
final class SeoData
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly string $keywords,
        public readonly string $canonical,
        public readonly string $robots,
        public readonly string $ogType,
        public readonly string $ogTitle,
        public readonly string $ogDescription,
        public readonly string $ogImage,
        public readonly string $ogUrl,
        public readonly string $siteName,
        public readonly string $twitterCard,
        public readonly string $twitterTitle,
        public readonly string $twitterDescription,
        public readonly string $twitterImage,
        public readonly string $twitterSite = '',
        public readonly string $locale = 'en_IN',
    ) {
    }

    /** Flat array consumed by the seo-head partial / layout. */
    public function toArray(): array
    {
        return [
            'title'               => $this->title,
            'description'         => $this->description,
            'keywords'            => $this->keywords,
            'canonical'           => $this->canonical,
            'robots'              => $this->robots,
            'og_type'             => $this->ogType,
            'og_title'            => $this->ogTitle,
            'og_description'      => $this->ogDescription,
            'og_image'            => $this->ogImage,
            'og_url'              => $this->ogUrl,
            'site_name'           => $this->siteName,
            'twitter_card'        => $this->twitterCard,
            'twitter_title'       => $this->twitterTitle,
            'twitter_description' => $this->twitterDescription,
            'twitter_image'       => $this->twitterImage,
            'twitter_site'        => $this->twitterSite,
            'locale'              => $this->locale,
        ];
    }
}
