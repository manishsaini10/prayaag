<?php

namespace App\Core\Mail;

use App\Core\Mail\Contracts\MailProviderInterface;
use App\Core\Mail\DTO\EmailMessage;
use App\Core\Mail\DTO\SendResult;
use App\Core\Mail\Providers\LogProvider;
use App\Jobs\SendEmailJob;
use App\Models\EmailLog;
use App\Models\EmailProviderConfig;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Log;

class MailManager
{
    protected TemplateRenderer $renderer;

    public function __construct(?TemplateRenderer $renderer = null)
    {
        $this->renderer = $renderer ?? new TemplateRenderer();
    }

    /**
     * Public entry point for sending emails across all modules.
     * By default ($async = false), emails are dispatched and sent immediately in real-time!
     */
    public function send(string $templateKey, array $data, string|array $to, ?array $overrideOptions = null, bool $async = false): ?EmailLog
    {
        $template = EmailTemplate::where('template_key', $templateKey)->first();

        if (!$template || !$template->is_active) {
            Log::warning("MailManager: Template '{$templateKey}' missing or inactive. Skipping email send.");
            return null;
        }

        $toAddresses = is_array($to) ? $to : [$to];
        $primaryTo = $toAddresses[0] ?? '';
        if (empty($primaryTo)) return null;

        $isNewsletter = ($template->module === 'newsletter');
        $unsubscribeUrl = $overrideOptions['unsubscribe_url'] ?? null;

        $rendered = $this->renderer->render(
            $template->body_html,
            $template->subject,
            $data,
            $isNewsletter,
            $unsubscribeUrl
        );

        $message = new EmailMessage(
            to: $toAddresses,
            subject: $rendered['subject'],
            bodyHtml: $rendered['body_html'],
            bodyText: $rendered['body_text'],
            fromName: $overrideOptions['from_name'] ?? null,
            fromEmail: $overrideOptions['from_email'] ?? null,
            replyTo: $overrideOptions['reply_to'] ?? null,
            templateKey: $templateKey,
            module: $template->module,
            metadata: $data
        );

        $log = EmailLog::create([
            'template_key' => $templateKey,
            'module'       => $template->module,
            'to_address'   => implode(', ', $toAddresses),
            'subject'      => $rendered['subject'],
            'status'       => 'queued',
        ]);

        // If background async is requested AND queue connection is configured
        if ($async && config('queue.default') !== 'sync') {
            SendEmailJob::dispatch($log->id, $message);
        } else {
            // Immediate real-time execution
            $this->executeSend($log, $message);
        }

        return $log->fresh();
    }

    /**
     * Executes the actual email send attempt with failover across configured providers.
     * Called synchronously or inside SendEmailJob.
     */
    public function executeSend(EmailLog $log, EmailMessage $message): SendResult
    {
        $providers = EmailProviderConfig::active()->ordered()->get();

        if ($providers->isEmpty()) {
            // Fallback to LogProvider if no active provider configured
            $logProvider = new LogProvider();
            $result = $logProvider->send($message);
            $log->update([
                'status'        => 'sent',
                'provider_used' => $logProvider->key(),
                'sent_at'       => now(),
            ]);
            return $result;
        }

        $lastError = null;
        foreach ($providers as $config) {
            try {
                $provider = $config->getProviderInstance();
                $result = $provider->send($message);

                if ($result->success) {
                    $log->update([
                        'status'        => 'sent',
                        'provider_used' => $config->provider_key,
                        'error_message' => null,
                        'sent_at'       => now(),
                    ]);
                    return $result;
                }

                $lastError = $result->error;
                Log::warning("MailManager: Provider '{$config->provider_key}' failed: {$result->error}. Trying next fallback.");
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                Log::error("MailManager: Provider '{$config->provider_key}' exception: {$e->getMessage()}");
            }
        }

        // All providers failed
        $log->update([
            'status'        => 'failed',
            'error_message' => "All providers failed. Last error: {$lastError}",
        ]);

        return SendResult::fail("All providers failed. Last error: {$lastError}");
    }
}
