<?php

return [
    'graph_version'   => env('FACEBOOK_GRAPH_VERSION', 'v23.0'),
    'app_id'          => env('FACEBOOK_APP_ID', ''),
    'app_secret'      => env('FACEBOOK_APP_SECRET', ''),
    'redirect_uri'    => env('FACEBOOK_REDIRECT_URI', '/admin/instagram/oauth/callback'),
    'cache_duration'  => env('INSTAGRAM_CACHE_DURATION', 3600),
    'sync_interval'   => env('INSTAGRAM_SYNC_INTERVAL', 60),
    'enable_queue'    => env('INSTAGRAM_ENABLE_QUEUE', true),
    'enable_local_cache' => env('INSTAGRAM_LOCAL_CACHE', true),
    'enable_webp'     => env('INSTAGRAM_WEBP', false),
    'log_channel'     => env('INSTAGRAM_LOG_CHANNEL', 'stack'),

    'scopes' => [
        'instagram_business_basic',
        'pages_show_list',
        'pages_read_engagement',
        'business_management',
        'instagram_business_manage_messages',
        'instagram_business_manage_comments',
        'pages_manage_metadata',
    ],
];
