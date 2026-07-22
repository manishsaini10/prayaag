<?php

namespace App\Core\Media\Services;

use Illuminate\Support\Facades\Http;

class CdnPurgeService
{
    public static function purge(string $url): void
    {
        if (!config('cdn.enabled') || !config('services.bunnycdn.api_key')) {
            return;
        }

        // Call BunnyCDN API to purge a specific file URL cache
        rescue(function () use ($url) {
            Http::withHeaders([
                'AccessKey' => config('services.bunnycdn.api_key'),
            ])->post('https://api.bunny.net/purge', [
                'url' => $url,
            ]);
        }, null, false);
    }
}
