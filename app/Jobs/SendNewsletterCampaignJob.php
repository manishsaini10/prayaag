<?php

namespace App\Jobs;

use App\Core\Mail\MailManager;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNewsletterCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600;

    public function __construct(public readonly string $campaignId)
    {
        $this->onQueue('emails');
    }

    public function handle(MailManager $mailManager): void
    {
        $campaign = NewsletterCampaign::find($this->campaignId);
        if (!$campaign || $campaign->status === 'sent') return;

        $campaign->update(['status' => 'sending']);

        $subscribers = NewsletterSubscriber::subscribed()->get();
        $total = $subscribers->count();
        $campaign->update(['recipient_count' => $total]);

        $sentCount = 0;
        foreach ($subscribers as $subscriber) {
            try {
                $mailManager->send(
                    templateKey: 'newsletter_campaign',
                    data: [
                        'subscriber_name' => $subscriber->name ?? 'Valued Parent',
                        'campaign_subject' => $campaign->subject,
                        'campaign_body' => $campaign->body_html,
                    ],
                    to: $subscriber->email,
                    overrideOptions: [
                        'unsubscribe_url' => $subscriber->unsubscribeUrl(),
                    ]
                );
                $sentCount++;
            } catch (\Throwable $e) {
                Log::error("Newsletter Campaign {$this->campaignId} failed for subscriber {$subscriber->id}: {$e->getMessage()}");
            }
        }

        $campaign->update([
            'status' => 'sent',
            'sent_count' => $sentCount,
            'sent_at' => now(),
        ]);
    }
}
