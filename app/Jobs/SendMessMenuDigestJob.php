<?php

namespace App\Jobs;

use App\Core\Mail\MailManager;
use App\Models\Mess\MessMenu;
use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMessMenuDigestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(MailManager $mailManager): void
    {
        $activeMenu = MessMenu::active()->with('items')->latest()->first();
        if (!$activeMenu) return;

        $subscribers = NewsletterSubscriber::subscribed()->get();

        $menuHtml = '<ul>';
        foreach ($activeMenu->items as $item) {
            $menuHtml .= "<li><strong>{$item->day_of_week} ({$item->meal_type}):</strong> {$item->dish_name}</li>";
        }
        $menuHtml .= '</ul>';

        foreach ($subscribers as $sub) {
            $mailManager->send(
                templateKey: 'mess_menu_weekly_digest',
                data: [
                    'menu_title' => $activeMenu->title,
                    'menu_items_html' => $menuHtml,
                    'pdf_link' => url('/mess-menu/pdf'),
                ],
                to: $sub->email,
                overrideOptions: [
                    'unsubscribe_url' => $sub->unsubscribeUrl(),
                ]
            );
        }
    }
}
