<?php

namespace Tests\Feature\Mail;

use App\Core\Mail\Contracts\MailProviderInterface;
use App\Core\Mail\DTO\EmailMessage;
use App\Core\Mail\DTO\SendResult;
use App\Core\Mail\MailManager;
use App\Core\Mail\Providers\LogProvider;
use App\Core\Mail\TemplateRenderer;
use App\Models\EmailLog;
use App\Models\EmailProviderConfig;
use App\Models\EmailTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MailManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_template_renderer_replaces_placeholders_correctly()
    {
        $renderer = new TemplateRenderer();
        $result = $renderer->render(
            '<h1>Hello {{candidate_name}}</h1>',
            'Application for {{job_title}}',
            ['candidate_name' => 'John Doe', 'job_title' => 'Math Teacher']
        );

        $this->assertEquals('Application for Math Teacher', $result['subject']);
        $this->assertStringContainsString('Hello John Doe', $result['body_html']);
    }

    public function test_newsletter_template_auto_appends_unsubscribe_footer()
    {
        $renderer = new TemplateRenderer();
        $result = $renderer->render(
            '<h1>Weekly Digest</h1>',
            'Weekly Digest',
            [],
            isNewsletter: true,
            unsubscribeUrl: 'http://localhost/newsletter/unsubscribe/123'
        );

        $this->assertStringContainsString('Unsubscribe from these emails', $result['body_html']);
        $this->assertStringContainsString('http://localhost/newsletter/unsubscribe/123', $result['body_html']);
    }

    public function test_mail_manager_dispatches_and_uses_log_provider_when_no_active_config()
    {
        EmailTemplate::create([
            'template_key' => 'test_template',
            'module' => 'careers',
            'subject' => 'Test Subject',
            'body_html' => '<p>Test Body</p>',
            'is_active' => true,
        ]);

        $manager = app(MailManager::class);
        $log = $manager->send('test_template', [], 'test@example.com');

        $this->assertNotNull($log);
        $this->assertEquals('queued', $log->status);
        $this->assertEquals('test_template', $log->template_key);
    }
}
