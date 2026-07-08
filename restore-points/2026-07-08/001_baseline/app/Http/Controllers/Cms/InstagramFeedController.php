<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\InstagramAccount;
use App\Models\InstagramMedia;
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

        $query = InstagramMedia::query()->published();

        if ($accountId && $account = InstagramAccount::find($accountId)) {
            $query->where('instagram_account_id', $account->id);
        } else {
            $account = InstagramAccount::connected()->first();
            if ($account) {
                $query->where('instagram_account_id', $account->id);
            }
        }

        if ($type && in_array($type, ['IMAGE', 'VIDEO', 'CAROUSEL_ALBUM'])) {
            $query->where('media_type', $type);
        }

        $total = $query->count();
        $media = $query->latest('posted_at')->skip($offset)->take($limit)->get()->map(fn ($m) => [
            'id' => $m->id,
            'media_id' => $m->media_id,
            'caption' => $m->caption,
            'media_type' => $m->media_type,
            'media_url' => $m->media_url,
            'thumbnail_url' => $m->thumbnail_url,
            'permalink' => $m->permalink,
            'likes' => $m->likes,
            'comments' => $m->comments,
            'timestamp' => $m->posted_at?->toIso8601String(),
            'username' => $m->account?->username,
            'children' => $m->children ? collect($m->children)->map(fn ($c) => [
                'media_url' => $c['media_url'] ?? '',
                'media_type' => $c['media_type'] ?? 'IMAGE',
            ]) : [],
        ]);

        return response()->json([
            'data' => $media,
            'total' => $total,
            'offset' => $offset,
            'limit' => $limit,
            'has_more' => ($offset + $limit) < $total,
        ]);
    }
}
