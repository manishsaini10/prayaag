<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Services\Instagram\FeedCacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstagramFeedController extends Controller
{
    public function __construct(
        private readonly FeedCacheService $cache,
    ) {}

    public function feed(Request $request): JsonResponse
    {
        $accountId = $request->input('account_id');
        $offset = max(0, (int) $request->input('offset', 0));
        $limit = min(50, max(1, (int) $request->input('limit', 12)));
        $type = $request->input('type');

        // Use FeedCacheService so query results are served from cache
        $media = $this->cache->getFeed($accountId, $limit, $offset, $type);

        return response()->json([
            'data' => $media,
            'meta' => [
                'count' => count($media),
                'offset' => $offset,
                'limit' => $limit,
            ]
        ]);
    }
}
