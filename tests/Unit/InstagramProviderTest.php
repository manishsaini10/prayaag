<?php

namespace Tests\Unit;

use App\Core\Video\Providers\InstagramReelProvider;

class InstagramProviderTest
{
    private InstagramReelProvider $provider;

    public function setUp(): void
    {
        $this->provider = new InstagramReelProvider();
    }

    public function test_extracts_shortcode_from_reel_url(): void
    {
        $url = 'https://www.instagram.com/reel/C1234567890/';
        $shortcode = $this->provider->extractShortcode($url);
        if ($shortcode !== 'C1234567890') {
            throw new \Exception("Expected C1234567890, got $shortcode");
        }
    }

    public function test_extracts_shortcode_from_post_url(): void
    {
        $url = 'https://www.instagram.com/p/C1234567890/';
        $shortcode = $this->provider->extractShortcode($url);
        if ($shortcode !== 'C1234567890') {
            throw new \Exception("Expected C1234567890, got $shortcode");
        }
    }

    public function test_extracts_shortcode_from_bare_id(): void
    {
        $idStr = 'C1234567890';
        $shortcode = $this->provider->extractShortcode($idStr);
        if ($shortcode !== 'C1234567890') {
            throw new \Exception("Expected C1234567890, got $shortcode");
        }
    }

    public function test_returns_null_for_invalid_input(): void
    {
        if ($this->provider->extractShortcode('https://example.com') !== null) {
            throw new \Exception("Expected null for invalid domain");
        }
    }

    public function test_get_embed_url(): void
    {
        $embed = $this->provider->getEmbedUrl('C1234567890');
        if ($embed !== 'https://www.instagram.com/p/C1234567890/embed/') {
            throw new \Exception("Expected https://www.instagram.com/p/C1234567890/embed/, got $embed");
        }
    }
}
