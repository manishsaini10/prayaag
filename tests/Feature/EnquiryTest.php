<?php

namespace Tests\Feature;

use App\Core\Builder\WidgetRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnquiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_submit_an_enquiry(): void
    {
        $this->post('/enquiries', [
            'name'    => 'Jane',
            'email'   => 'jane@example.test',
            'message' => 'Hello there',
            'type'    => 'contact',
        ])->assertRedirect();

        $this->assertDatabaseHas('enquiries', [
            'email' => 'jane@example.test',
            'name'  => 'Jane',
        ]);
    }

    public function test_honeypot_silently_drops_bots(): void
    {
        $this->post('/enquiries', [
            'name'    => 'Bot',
            'email'   => 'bot@example.test',
            'message' => 'spam',
            'website' => 'http://spam.example',
        ])->assertRedirect();

        $this->assertDatabaseCount('enquiries', 0);
    }

    public function test_validation_requires_email_and_message(): void
    {
        $this->post('/enquiries', ['name' => 'NoEmail'])
            ->assertSessionHasErrors(['email', 'message']);

        $this->assertDatabaseCount('enquiries', 0);
    }

    public function test_contact_form_widget_renders_a_form(): void
    {
        $html = app(WidgetRegistry::class)->render('contact_form', []);

        $this->assertStringContainsString('<form', $html);
        $this->assertStringContainsString('name="email"', $html);
        $this->assertStringContainsString('/enquiries', $html);
    }
}
