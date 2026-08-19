<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Public newsletter signup. Idempotent per email: a repeat signup
 * re-activates an existing record rather than erroring on the unique index.
 */
class SubscriberController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        // Honeypot: bots fill the hidden "website" field. Silently drop them.
        if ($request->filled('website')) {
            return back();
        }

        $data = $request->validate([
            'email'  => 'required|email|max:255',
            'name'   => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
        ]);

        $subscriber = \App\Models\NewsletterSubscriber::createPending(
            email: $data['email'],
            source: $data['source'] ?? 'website_footer',
            name: $data['name'] ?? null
        );

        if ($subscriber->confirm_token) {
            $confirmUrl = route('newsletter.confirm', ['token' => $subscriber->confirm_token]);
            try {
                app(\App\Core\Mail\MailManager::class)->send('newsletter_subscribe_confirm', [
                    'confirm_url' => $confirmUrl,
                ], $subscriber->email);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send newsletter confirm email: " . $e->getMessage());
            }
        }

        return back()->with('subscribed', true);
    }
}
