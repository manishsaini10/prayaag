<?php

namespace Tests\Unit;

use App\Core\Video\Providers\YouTubeUnlistedProvider;

class YouTubeProviderTest
{
    private YouTubeUnlistedProvider $provider;

    public function setUp(): void
    {
        $this->provider = new YouTubeUnlistedProvider();
    }

    public function test_extracts_id_from_standard_watch_url(): void
    {
        $url = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
        $id = $this->provider->extractVideoId($url);
        if ($id !== 'dQw4w9WgXcQ') {
            throw new \Exception("Expected dQw4w9WgXcQ, got $id");
        }
    }

    public function test_extracts_id_from_short_url(): void
    {
        $url = 'https://youtu.be/dQw4w9WgXcQ';
        $id = $this->provider->extractVideoId($url);
        if ($id !== 'dQw4w9WgXcQ') {
            throw new \Exception("Expected dQw4w9WgXcQ, got $id");
        }
    }

    public function test_extracts_id_from_shorts_url(): void
    {
        $url = 'https://www.youtube.com/shorts/dQw4w9WgXcQ';
        $id = $this->provider->extractVideoId($url);
        if ($id !== 'dQw4w9WgXcQ') {
            throw new \Exception("Expected dQw4w9WgXcQ, got $id");
        }
    }

    public function test_extracts_id_from_bare_id(): void
    {
        $idStr = 'dQw4w9WgXcQ';
        $id = $this->provider->extractVideoId($idStr);
        if ($id !== 'dQw4w9WgXcQ') {
            throw new \Exception("Expected dQw4w9WgXcQ, got $id");
        }
    }

    public function test_returns_null_for_invalid_input(): void
    {
        if ($this->provider->extractVideoId('https://example.com') !== null) {
            throw new \Exception("Expected null for invalid domain");
        }
        if ($this->provider->extractVideoId('invalid_string') !== null) {
            throw new \Exception("Expected null for invalid string");
        }
    }

    public function test_get_thumbnail_url_returns_valid_hqdefault_link(): void
    {
        $thumb = $this->provider->getThumbnailUrl('dQw4w9WgXcQ');
        if ($thumb !== 'https://img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg') {
            throw new \Exception("Expected https://img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg, got $thumb");
        }
    }
}
