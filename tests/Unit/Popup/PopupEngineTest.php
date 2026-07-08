<?php

namespace Tests\Unit\Popup;

use App\Core\Popup\Engines\RenderingEngine;
use App\Core\Popup\Engines\TriggerEngine;
use App\Models\Popup\Popup;
use App\Models\Popup\PopupRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PopupEngineTest extends TestCase
{
    use RefreshDatabase;

    private RenderingEngine $renderingEngine;
    private TriggerEngine $triggerEngine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->renderingEngine = app(RenderingEngine::class);
        $this->triggerEngine = app(TriggerEngine::class);
    }

    /** @test */
    public function it_renders_heading_widget()
    {
        $widget = [
            'type' => 'heading',
            'content' => 'Test Heading',
            'settings' => ['tag' => 'h2', 'align' => 'center', 'color' => '#333', 'fontSize' => '24'],
        ];

        $html = $this->renderingEngine->renderWidget('heading', $widget);

        $this->assertStringContainsString('<h2', $html);
        $this->assertStringContainsString('Test Heading', $html);
        $this->assertStringContainsString('color:#333', $html);
    }

    /** @test */
    public function it_renders_button_widget()
    {
        $widget = [
            'type' => 'button',
            'content' => 'Click Me',
            'settings' => ['url' => '#', 'backgroundColor' => '#6366f1', 'textColor' => '#fff', 'borderRadius' => '8'],
        ];

        $html = $this->renderingEngine->renderWidget('button', $widget);

        $this->assertStringContainsString('Click Me', $html);
        $this->assertStringContainsString('background-color:#6366f1', $html);
        $this->assertStringContainsString('href="#"', $html);
    }

    /** @test */
    public function it_renders_image_widget()
    {
        $widget = [
            'type' => 'image',
            'content' => '',
            'settings' => ['src' => '/test.jpg', 'alt' => 'Test Image', 'width' => '300', 'align' => 'center'],
        ];

        $html = $this->renderingEngine->renderWidget('image', $widget);

        $this->assertStringContainsString('<img', $html);
        $this->assertStringContainsString('src="/test.jpg"', $html);
        $this->assertStringContainsString('alt="Test Image"', $html);
    }

    /** @test */
    public function it_renders_spacer_widget()
    {
        $widget = ['type' => 'spacer', 'settings' => ['height' => '50']];

        $html = $this->renderingEngine->renderWidget('spacer', $widget);

        $this->assertStringContainsString('height:50px', $html);
    }

    /** @test */
    public function it_renders_newsletter_form_widget()
    {
        $widget = ['type' => 'newsletter_form', 'settings' => [
            'buttonText' => 'Subscribe',
            'buttonColor' => '#6366f1',
            'placeholder' => 'Enter email',
        ]];

        $html = $this->renderingEngine->renderWidget('newsletter_form', $widget);

        $this->assertStringContainsString('Subscribe', $html);
        $this->assertStringContainsString('Enter email', $html);
        $this->assertStringContainsString('popup-newsletter-form', $html);
    }

    /** @test */
    public function it_detects_trigger_type()
    {
        $popup = Popup::factory()->create();
        $popup->rules()->create([
            'type' => 'trigger',
            'rule_key' => 'exit_intent',
            'condition' => 'is',
            'value' => 'true',
        ]);

        $this->assertEquals('exit_intent', $this->triggerEngine->getTriggerType($popup));
    }

    /** @test */
    public function it_gets_trigger_delay()
    {
        $popup = Popup::factory()->create();
        $popup->rules()->create([
            'type' => 'trigger',
            'rule_key' => 'time_delay',
            'condition' => 'is',
            'value' => '3000',
        ]);

        $this->assertEquals(3000, $this->triggerEngine->getTriggerDelay($popup));
    }

    /** @test */
    public function it_returns_zero_delay_when_no_delay_trigger()
    {
        $popup = Popup::factory()->create();
        $popup->rules()->create([
            'type' => 'trigger',
            'rule_key' => 'page_load',
            'condition' => 'is',
            'value' => 'true',
        ]);

        $this->assertEquals(0, $this->triggerEngine->getTriggerDelay($popup));
    }

    /** @test */
    public function it_gets_scroll_percent()
    {
        $popup = Popup::factory()->create();
        $popup->rules()->create([
            'type' => 'trigger',
            'rule_key' => 'scroll_percent',
            'condition' => 'is',
            'value' => '75',
        ]);

        $this->assertEquals(75, $this->triggerEngine->getScrollPercent($popup));
    }

    /** @test */
    public function it_gets_default_scroll_percent()
    {
        $popup = Popup::factory()->create();

        $this->assertEquals(50, $this->triggerEngine->getScrollPercent($popup));
    }

    /** @test */
    public function it_gets_click_selector()
    {
        $popup = Popup::factory()->create();
        $popup->rules()->create([
            'type' => 'trigger',
            'rule_key' => 'click',
            'condition' => 'is',
            'value' => '.cta-button',
        ]);

        $this->assertEquals('.cta-button', $this->triggerEngine->getClickSelector($popup));
    }

    /** @test */
    public function it_returns_null_when_no_click_trigger()
    {
        $popup = Popup::factory()->create();

        $this->assertNull($this->triggerEngine->getClickSelector($popup));
    }
}
