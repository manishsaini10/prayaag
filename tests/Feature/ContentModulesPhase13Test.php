<?php

namespace Tests\Feature;

use App\Core\Builder\WidgetRegistry;
use App\Models\Gallery;
use App\Models\Slider;
use App\Models\Subscriber;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentModulesPhase13Test extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_has_ordered_images(): void
    {
        $gallery = Gallery::create(['title' => 'G', 'slug' => 'g']);
        $gallery->images()->create(['image' => 'b.jpg', 'sort_order' => 2]);
        $gallery->images()->create(['image' => 'a.jpg', 'sort_order' => 1]);

        $this->assertSame('a.jpg', $gallery->images()->first()->image);
        $this->assertSame(2, $gallery->images()->count());
    }

    public function test_public_can_subscribe(): void
    {
        $this->post('/subscribe', ['email' => 'fan@example.test'])->assertRedirect();

        $this->assertDatabaseHas('subscribers', [
            'email'  => 'fan@example.test',
            'status' => 'subscribed',
        ]);
    }

    public function test_subscribing_twice_is_idempotent(): void
    {
        $this->post('/subscribe', ['email' => 'dup@example.test']);
        $this->post('/subscribe', ['email' => 'dup@example.test']);

        $this->assertSame(1, Subscriber::where('email', 'dup@example.test')->count());
    }

    public function test_subscribe_honeypot_drops_bots(): void
    {
        $this->post('/subscribe', ['email' => 'bot@example.test', 'website' => 'spam'])->assertRedirect();

        $this->assertDatabaseCount('subscribers', 0);
    }

    public function test_slider_widget_renders_slides(): void
    {
        $slider = Slider::create(['title' => 'Hero', 'location' => 'homepage']);
        $slider->slides()->create(['image' => 'hero.jpg', 'heading' => 'Welcome Aboard', 'sort_order' => 0]);

        $html = app(WidgetRegistry::class)->render('slider', ['location' => 'homepage']);

        $this->assertStringContainsString('Welcome Aboard', $html);
        $this->assertStringContainsString('hero.jpg', $html);
    }

    public function test_testimonials_widget_renders_published(): void
    {
        Testimonial::create(['author' => 'Aarti', 'role' => 'Parent', 'quote' => 'Wonderful school']);

        $html = app(WidgetRegistry::class)->render('testimonials', ['limit' => 5]);

        $this->assertStringContainsString('Wonderful school', $html);
        $this->assertStringContainsString('Aarti', $html);
    }
}
