<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Video Provider
    |--------------------------------------------------------------------------
    | Supported: "youtube_unlisted", "cloudflare_stream", "local"
    | Switch providers here without touching any widget or controller code.
    */
    'default_provider' => env('VIDEO_DEFAULT_PROVIDER', 'youtube_unlisted'),

    /*
    |--------------------------------------------------------------------------
    | Cloudflare Stream
    |--------------------------------------------------------------------------
    */
    'cloudflare' => [
        'account_id'    => env('CLOUDFLARE_STREAM_ACCOUNT_ID'),
        'api_token'     => env('CLOUDFLARE_STREAM_API_TOKEN'),
        'customer_code' => env('CLOUDFLARE_STREAM_CUSTOMER_CODE'),
    ],

    /*
    |--------------------------------------------------------------------------
    | YouTube (Unlisted uploads — free fallback)
    |--------------------------------------------------------------------------
    */
    'youtube' => [
        'client_id'     => env('YOUTUBE_CLIENT_ID'),
        'client_secret' => env('YOUTUBE_CLIENT_SECRET'),
        'refresh_token' => env('YOUTUBE_CHANNEL_REFRESH_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Upload Limits
    |--------------------------------------------------------------------------
    */
    'max_upload_mb'      => env('VIDEO_MAX_UPLOAD_MB', 250),
    'allowed_mime_types' => ['video/mp4', 'video/quicktime', 'video/webm'],
];
