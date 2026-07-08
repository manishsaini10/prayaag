<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

/**
 * Performs configured URL redirects on incoming GET requests. Inert until the
 * `redirects` table exists, and cheap thereafter (single indexed lookup).
 * Admins should only add redirects for old/dead URLs, since a redirect takes
 * precedence over a live page at the same path.
 */
class HandleRedirects
{
    /** Cached per-process so a missing table never repeatedly errors. */
    protected static ?bool $tableExists = null;

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET') && $this->tableExists()) {
            $path = ltrim($request->path(), '/');
            $candidates = ['/' . $path, $path === '' ? '/' : $path];

            $redirect = rescue(
                fn () => Redirect::where('is_active', true)->whereIn('from_path', $candidates)->first(),
                null,
                false
            );

            if ($redirect) {
                rescue(fn () => $redirect->increment('hits'), null, false);

                return redirect($redirect->to_path, in_array($redirect->status_code, [301, 302], true) ? $redirect->status_code : 301);
            }
        }

        return $next($request);
    }

    protected function tableExists(): bool
    {
        if (static::$tableExists === null) {
            static::$tableExists = rescue(fn () => Schema::hasTable('redirects'), false, false);
        }

        return static::$tableExists;
    }
}
