<?php

return [
    'enabled' => env('CDN_ENABLED', false),
    'url'     => env('CDN_URL'),
    'driver'  => env('CDN_DRIVER', 'bunnycdn'),
];
