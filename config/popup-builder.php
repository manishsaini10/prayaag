<?php

return [
    'prefix' => 'popup',
    'middleware' => ['web', 'auth'],
    'api_middleware' => ['api', 'throttle:60,1'],
    'route_prefix' => 'admin/popup-builder',
    'api_route_prefix' => 'api/v1/popup',
    'cache' => [
        'ttl' => env('POPUP_CACHE_TTL', 3600),
        'prefix' => 'popup_',
        'store' => env('POPUP_CACHE_STORE', 'file'),
    ],
    'queue' => [
        'connection' => env('POPUP_QUEUE_CONNECTION', 'sync'),
        'analytics_queue' => env('POPUP_ANALYTICS_QUEUE', 'default'),
    ],
    'media' => [
        'disk' => env('POPUP_MEDIA_DISK', 'public'),
        'max_size' => env('POPUP_MEDIA_MAX_SIZE', 2048), // KB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'mp4', 'webm'],
    ],
    'analytics' => [
        'enabled' => env('POPUP_ANALYTICS_ENABLED', true),
        'track_impressions' => true,
        'track_clicks' => true,
        'track_conversions' => true,
        'track_views' => true,
        'ip_anonymization' => env('POPUP_IP_ANONYMIZATION', true),
        'retention_days' => env('POPUP_ANALYTICS_RETENTION_DAYS', 365),
    ],
    'design' => [
        'google_fonts_enabled' => env('POPUP_GOOGLE_FONTS_ENABLED', true),
        'max_width' => 1200,
        'max_height' => 900,
        'animations' => [
            'fade', 'zoom', 'slide', 'bounce', 'rotate',
            'elastic', 'scale', 'flip', 'pulse', 'shake',
        ],
    ],
    'performance' => [
        'lazy_load' => true,
        'defer_js' => true,
        'minify_assets' => env('POPUP_MINIFY_ASSETS', true),
        'concurrent_requests' => 3,
        'ajax_loading' => true,
    ],
    'security' => [
        'rate_limit' => [
            'popup_views' => 100,
            'popup_interactions' => 50,
            'decay_minutes' => 1,
        ],
        'allowed_origins' => env('POPUP_ALLOWED_ORIGINS', '*'),
        'content_security_policy' => env('POPUP_CSP_ENABLED', false),
    ],
    'features' => [
        'ab_testing' => env('POPUP_AB_TESTING_ENABLED', true),
        'lead_capture' => env('POPUP_LEAD_CAPTURE_ENABLED', true),
        'scheduling' => env('POPUP_SCHEDULING_ENABLED', true),
        'geo_targeting' => env('POPUP_GEO_TARGETING_ENABLED', false),
        'revisions' => env('POPUP_REVISIONS_ENABLED', true),
        'templates' => env('POPUP_TEMPLATES_ENABLED', true),
        'webhooks' => env('POPUP_WEBHOOKS_ENABLED', true),
    ],
    'defaults' => [
        'animation' => 'fade',
        'position' => 'center-center',
        'width' => 600,
        'height' => 400,
        'overlay' => true,
        'close_button' => true,
        'esc_close' => true,
        'overlay_close' => true,
        'z_index' => 999999,
    ],
];
