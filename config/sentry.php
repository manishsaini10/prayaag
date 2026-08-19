<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sentry DSN & Environment Configuration
    |--------------------------------------------------------------------------
    |
    | Sentry DSN for error monitoring & performance tracing.
    | When empty, exception reporting is disabled gracefully (dev mode).
    |
    */

    'dsn' => env('SENTRY_LARAVEL_DSN', env('SENTRY_DSN', '')),

    'environment' => env('APP_ENV', 'production'),

    'release' => env('APP_VERSION', '1.0.0'),

    // Sample rate for performance tracing (0.0 to 1.0)
    'traces_sample_rate' => (float) env('SENTRY_TRACES_SAMPLE_RATE', 0.2),

    // Sensitive field names to automatically redact before sending to Sentry
    'send_default_pii' => false,

    'sensitive_fields' => [
        'password',
        'password_confirmation',
        'g-recaptcha-response',
        'credit_card',
        'card_number',
        'cvv',
        'secret',
    ],

];
