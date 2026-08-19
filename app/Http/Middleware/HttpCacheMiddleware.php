<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * HttpCacheMiddleware — Adds browser and CDN Cache-Control headers + ETag validation.
 *
 * Usage in routes/web.php:
 *   Route::get('/mess-menu', ...)->middleware('http.cache:300'); // max-age 300s (5 mins)
 *
 * If the incoming request has `If-None-Match` matching the response ETag,
 * returns HTTP 304 Not Modified with empty body to save bandwidth and load.
 */
class HttpCacheMiddleware
{
    public function handle(Request $request, Closure $next, int $maxAge = 300, int $sharedMaxAge = 600): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Only cache successful GET requests
        if (! $request->isMethod('GET') || $response->getStatusCode() !== 200) {
            return $response;
        }

        // Set Cache-Control headers
        $response->headers->set('Cache-Control', "public, max-age={$maxAge}, s-maxage={$sharedMaxAge}, must-revalidate");

        // Generate ETag hash based on response content
        $content = $response->getContent();
        if ($content !== false && strlen($content) > 0) {
            $etag = '"' . md5($content) . '"';
            $response->headers->set('ETag', $etag);

            // Check client If-None-Match header
            $clientEtag = $request->header('If-None-Match');
            if ($clientEtag && (trim($clientEtag) === $etag || trim($clientEtag) === '*')) {
                $response->setStatusCode(304);
                $response->setContent('');
            }
        }

        return $response;
    }
}
