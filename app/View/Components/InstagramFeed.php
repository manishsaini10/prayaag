<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Services\Instagram\FeedCacheService;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InstagramFeed extends Component
{
    public array $feed;
    public ?array $profile;
    public bool $hasMore;

    public function __construct(
        public ?string $accountId = null,
        public int $limit = 12,
        public string $layout = 'grid',
        public int $columnsDesktop = 4,
        public int $columnsTablet = 3,
        public int $columnsMobile = 2,
        public bool $showCaption = true,
        public bool $showLikes = false,
        public bool $showButton = true,
        public bool $showPopup = true,
        public bool $infiniteScroll = true,
        public ?string $filterType = null,
        public string $heading = 'Follow Us on Instagram',
        public ?string $subheading = null,
    ) {
        $cache = app(FeedCacheService::class);
        $result = $cache->getFeed($accountId, $limit, 0, $filterType);
        $this->feed = $result['data'];
        $this->hasMore = $result['has_more'];
        $this->profile = $cache->getProfile($accountId);
    }

    public function render(): View
    {
        return view('components.instagram-feed');
    }
}
