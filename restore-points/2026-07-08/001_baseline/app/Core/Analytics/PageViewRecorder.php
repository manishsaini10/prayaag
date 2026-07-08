<?php

namespace App\Core\Analytics;

use App\Models\Page;
use App\Models\PageView;
use Illuminate\Http\Request;

/**
 * Records one lightweight first-party page-view row per public request.
 * IPs are hashed, never stored raw.
 */
class PageViewRecorder
{
    public function record(Request $request, ?Page $page = null): void
    {
        PageView::create([
            'page_id'    => $page?->id,
            'path'       => mb_substr($request->path(), 0, 255),
            'referrer'   => $request->headers->get('referer'),
            'ip_hash'    => $request->ip()
                ? hash('sha256', $request->ip() . config('app.key'))
                : null,
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 512),
            'viewed_at'  => now(),
        ]);
    }
}
