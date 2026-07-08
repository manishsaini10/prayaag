<?php

namespace Tests\Feature;

use App\Core\Settings\SettingsManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_values_round_trip_with_type_casting(): void
    {
        $settings = app(SettingsManager::class);

        $settings->set('maintenance', true, 'boolean');
        $settings->set('per_page', 25, 'integer');
        $settings->set('payload', ['a' => 1], 'json');

        $this->assertSame(true, $settings->get('maintenance'));
        $this->assertSame(25, $settings->get('per_page'));
        $this->assertSame(['a' => 1], $settings->get('payload'));
        $this->assertSame('fallback', $settings->get('missing', 'fallback'));
    }

    public function test_set_overwrites_and_forget_removes(): void
    {
        $settings = app(SettingsManager::class);

        $settings->set('site_name', 'First');
        $this->assertSame('First', $settings->get('site_name'));

        // A second set updates in place (single global row per key).
        $settings->set('site_name', 'Second');
        $this->assertSame('Second', $settings->get('site_name'));

        $settings->forget('site_name');
        $this->assertNull($settings->get('site_name'));
    }
}
