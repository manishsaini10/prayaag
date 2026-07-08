<?php

namespace Tests\Unit\Popup;

use App\Core\Popup\DTOs\AnalyticsDTO;
use App\Core\Popup\DTOs\LeadDTO;
use App\Core\Popup\DTOs\PopupDTO;
use App\Core\Popup\DTOs\RuleDTO;
use Tests\TestCase;

class PopupDTOTest extends TestCase
{
    /** @test */
    public function it_creates_popup_dto_from_array()
    {
        $dto = PopupDTO::fromArray([
            'title' => 'Test Popup',
            'type' => 'modal',
            'status' => 'active',
            'structure' => ['rows' => []],
            'settings' => ['width' => 600],
        ]);

        $this->assertEquals('Test Popup', $dto->title);
        $this->assertEquals('modal', $dto->type);
        $this->assertEquals('active', $dto->status);
    }

    /** @test */
    public function it_generates_slug_from_title()
    {
        $dto = PopupDTO::fromArray([
            'title' => 'My Awesome Popup',
            'type' => 'modal',
        ]);

        $this->assertEquals('my-awesome-popup', $dto->slug);
    }

    /** @test */
    public function it_converts_popup_dto_to_array()
    {
        $dto = new PopupDTO(
            title: 'Test',
            slug: 'test',
            type: 'modal',
            status: 'draft',
        );

        $array = $dto->toArray();

        $this->assertEquals('Test', $array['title']);
        $this->assertEquals('test', $array['slug']);
        $this->assertEquals('draft', $array['status']);
    }

    /** @test */
    public function it_creates_rule_dto()
    {
        $dto = RuleDTO::fromArray([
            'type' => 'trigger',
            'rule_key' => 'page_load',
            'condition' => 'is',
            'value' => 'true',
        ]);

        $this->assertEquals('trigger', $dto->type);
        $this->assertEquals('page_load', $dto->ruleKey);
        $this->assertEquals('is', $dto->condition);
    }

    /** @test */
    public function it_creates_analytics_dto()
    {
        $dto = new AnalyticsDTO(
            popupId: '01-test-id',
            eventType: 'view',
            url: 'http://test.com',
            sessionId: 'sess-123',
        );

        $array = $dto->toArray();

        $this->assertEquals('01-test-id', $array['popup_id']);
        $this->assertEquals('view', $array['event_type']);
        $this->assertArrayHasKey('occurred_at', $array);
    }

    /** @test */
    public function it_creates_lead_dto()
    {
        $dto = new LeadDTO(
            popupId: '01-test-id',
            name: 'John',
            email: 'john@test.com',
            phone: '1234567890',
            formData: ['message' => 'Hello'],
        );

        $array = $dto->toArray();

        $this->assertEquals('John', $array['name']);
        $this->assertEquals('john@test.com', $array['email']);
        $this->assertEquals('new', $array['status']);
    }
}
