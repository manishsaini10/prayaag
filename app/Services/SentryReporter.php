<?php

namespace App\Services;

use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * Custom SentryReporter service.
 *
 * Captures uncaught exceptions, sanitizes PII data (passwords, tokens, credit cards),
 * and logs/reports to Sentry if SENTRY_DSN is set, or logs locally if Sentry is absent.
 */
class SentryReporter
{
    protected string $dsn;
    protected string $environment;
    protected array $sensitiveFields;

    public function __construct()
    {
        $this->dsn             = config('sentry.dsn', '');
        $this->environment     = config('sentry.environment', 'production');
        $this->sensitiveFields = config('sentry.sensitive_fields', []);
    }

    /**
     * Capture and report an exception.
     *
     * @param  Throwable  $exception
     * @param  array      $context
     */
    public function capture(Throwable $exception, array $context = []): void
    {
        // Sanitize context
        $sanitizedContext = $this->sanitize($context);

        // Always log locally
        Log::error('Exception captured: ' . $exception->getMessage(), array_merge([
            'exception' => get_class($exception),
            'file'      => $exception->getFile(),
            'line'      => $exception->getLine(),
        ], $sanitizedContext));

        // If Sentry DSN is configured and sentry package is available, report to Sentry
        if (! empty($this->dsn) && function_exists('Sentry\captureException')) {
            try {
                \Sentry\captureException($exception);
            } catch (Throwable $e) {
                Log::warning('Failed to send exception to Sentry: ' . $e->getMessage());
            }
        }
    }

    /**
     * Sanitize sensitive keys from context array.
     */
    public function sanitize(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitize($value);
            } elseif (in_array(strtolower((string) $key), $this->sensitiveFields, true)) {
                $data[$key] = '[REDACTED]';
            }
        }

        return $data;
    }
}
