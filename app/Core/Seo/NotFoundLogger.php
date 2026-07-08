<?php

namespace App\Core\Seo;

use App\Models\NotFoundLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

/**
 * Records 404s for the SEO 404 monitor. Designed to be cheap and crash-proof:
 * inert until the table exists, ignores bots/asset probes, and never throws
 * into the request lifecycle. Called from the exception render hook.
 */
class NotFoundLogger
{
    protected static ?bool $tableExists = null;

    /** Asset / probe paths we never want cluttering the report. */
    protected const IGNORE_PATTERNS = [
        '#\.(css|js|map|png|jpe?g|gif|svg|webp|ico|woff2?|ttf|eot|pdf|zip|txt|xml)$#i',
        '#^(admin|up|storage|build|vendor|favicon|apple-touch|wp-|\.well-known)#i',
    ];

    public function log(Request $request): void
    {
        if (! $request->isMethod('GET') || ! $this->tableExists()) {
            return;
        }

        $path = trim($request->path());
        if ($path === '' || $path === '/') {
            return;
        }
        foreach (self::IGNORE_PATTERNS as $re) {
            if (preg_match($re, $path)) {
                return;
            }
        }

        $referrer = mb_substr((string) $request->headers->get('referer', ''), 0, 255) ?: null;
        $ua = mb_substr((string) $request->userAgent(), 0, 512) ?: null;

        rescue(function () use ($path, $referrer, $ua) {
            $row = NotFoundLog::firstOrNew(['path' => $path]);
            $row->hits = ($row->exists ? $row->hits : 0) + 1;
            $row->referrer = $referrer ?: $row->referrer;
            $row->user_agent = $ua ?: $row->user_agent;
            $row->last_seen_at = now();
            // A path that 404s again is unresolved again.
            $row->resolved = false;
            $row->save();
        }, null, false);
    }

    protected function tableExists(): bool
    {
        if (static::$tableExists === null) {
            static::$tableExists = rescue(fn () => Schema::hasTable('not_found_logs'), false, false);
        }

        return static::$tableExists;
    }
}
