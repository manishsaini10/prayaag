<?php

namespace Tests\Feature\Mail;

use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class NewsletterFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscriber_creation_creates_pending_subscriber()
    {
        $subscriber = NewsletterSubscriber::createPending('parent@example.com', 'test_form', 'Parent Name');

        $this->assertEquals('pending', $subscriber->status);
        $this->assertNotNull($subscriber->confirm_token);
        $this->assertEquals('test_form', $subscriber->consent_source);
    }

    public function test_confirm_token_verifies_subscriber()
    {
        $subscriber = NewsletterSubscriber::createPending('parent@example.com', 'test_form');
        $token = $subscriber->confirm_token;

        $response = $this->get("/newsletter/confirm/{$token}");
        $response->assertStatus(200);
        $response->assertSee('Subscription Confirmed!');

        $subscriber->refresh();
        $this->assertEquals('subscribed', $subscriber->status);
        $this->assertNull($subscriber->confirm_token);
    }

    public function test_signed_unsubscribe_url_unsubscribes_subscriber()
    {
        $subscriber = NewsletterSubscriber::create([
            'email' => 'parent@example.com',
            'email_hash' => NewsletterSubscriber::hashEmail('parent@example.com'),
            'status' => 'subscribed',
            'subscribed_at' => now(),
        ]);

        $url = $subscriber->unsubscribeUrl();

        $response = $this->get($url);
        $response->assertStatus(200);
        $response->assertSee('Unsubscribed Successfully');

        $subscriber->refresh();
        $this->assertEquals('unsubscribed', $subscriber->status);
    }
}
