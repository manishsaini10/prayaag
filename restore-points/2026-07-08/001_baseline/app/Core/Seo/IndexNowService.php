<?php

namespace App\Core\Seo;

use App\Core\Settings\SettingsManager;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * IndexNow client (Phase 17). Instantly notifies participating search engines
 * (Bing, Yandex, Seznam, Naver — and any IndexNow-compatible engine) when a URL
 * is published, updated, or removed. One submission is shared across all of
 * them via the IndexNow protocol.
 *
 * NOTE: Google does NOT use IndexNow and deprecated its sitemap-ping endpoint
 * in 2023 — Googlebot rediscovers content via crawl + Search Console. So this
 * covers Bing/Yandex/etc.; Google indexing relies on the sitemap + crawling.
 *
 * Inert unless an IndexNow key is configured (Settings → SEO). Never throws.
 */
class IndexNowService
{
    protected const ENDPOINT = 'https://api.indexnow.org/indexnow';

    public function __construct(protected SettingsManager $settings)
    {
    }

    public function enabled(): bool
    {
        return trim((string) $this->settings->get('seo_indexnow_key', '')) !== '';
    }

    /** Submit one or more absolute URLs. Returns true on a 2xx response. */
    public function submit(array $urls): bool
    {
        $key = trim((string) $this->settings->get('seo_indexnow_key', ''));
        $urls = array_values(array_filter(array_unique($urls), fn ($u) => is_string($u) && str_starts_with($u, 'http')));

        if ($key === '' || $urls === []) {
            return false;
        }

        $host = parse_url(url('/'), PHP_URL_HOST) ?: '';

        return rescue(function () use ($key, $urls, $host) {
            $res = Http::timeout(5)->acceptJson()->post(self::ENDPOINT, [
                'host'        => $host,
                'key'         => $key,
                'keyLocation' => url('/' . $key . '.txt'),
                'urlList'     => array_values($urls),
            ]);

            if (! $res->successful()) {
                Log::info('IndexNow non-2xx', ['status' => $res->status()]);
            }

            return $res->successful();
        }, false, false);
    }

    /** Convenience: submit a single URL. */
    public function ping(string $url): bool
    {
        return $this->submit([$url]);
    }
}
