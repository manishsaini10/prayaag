<?php

namespace App\Core\Media\Services;

class CdnUrlService
{
    public static function url(string $url): string
    {
        if (!config('cdn.enabled') || !config('cdn.url')) {
            return $url;
        }

        $parsed = parse_url($url);
        $cdnBase = rtrim(config('cdn.url'), '/');
        
        $path = $parsed['path'] ?? '';
        $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
        $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

        return "{$cdnBase}{$path}{$query}{$fragment}";
    }
}
