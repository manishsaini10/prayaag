<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google reCAPTCHA v3
    |--------------------------------------------------------------------------
    |
    | Site key is embedded in the public JS widget.
    | Secret key is used server-side to verify the token score.
    | Minimum score: 0.0 (bot) to 1.0 (human) — 0.5 is a safe default.
    |
    */
    'recaptcha_site_key'   => env('RECAPTCHA_SITE_KEY', ''),
    'recaptcha_secret_key' => env('RECAPTCHA_SECRET_KEY', ''),
    'recaptcha_score'      => env('RECAPTCHA_MIN_SCORE', 0.5),

    /*
    |--------------------------------------------------------------------------
    | Data Retention (GDPR / FERPA)
    |--------------------------------------------------------------------------
    |
    | Personal data in job_applications and enquiries will be anonymised
    | (soft-deleted + PII cleared) after this many months of inactivity.
    | Set to 0 to disable automatic purging.
    |
    */
    'retention_months' => env('PERSONAL_DATA_RETENTION_MONTHS', 24),

    /*
    |--------------------------------------------------------------------------
    | ClamAV Malware Scanning
    |--------------------------------------------------------------------------
    |
    | When clamav_enabled is true, uploaded files are piped to the clamdscan
    | binary before being stored. If ClamAV is unavailable the upload is
    | rejected (fail-closed) unless clamav_fail_open is set to true.
    |
    */
    'clamav_enabled'   => env('CLAMAV_ENABLED', false),
    'clamav_binary'    => env('CLAMAV_BINARY', 'clamdscan'),
    'clamav_fail_open' => env('CLAMAV_FAIL_OPEN', false),

];
