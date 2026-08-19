<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function confirm(string $token)
    {
        $subscriber = NewsletterSubscriber::where('confirm_token', $token)->first();

        if (!$subscriber) {
            return view('cms.newsletter-response', [
                'success' => false,
                'title' => 'Invalid or Expired Link',
                'message' => 'The confirmation link is invalid or has expired.',
            ]);
        }

        $subscriber->update([
            'status' => 'subscribed',
            'confirm_token' => null,
            'subscribed_at' => now(),
        ]);

        return view('cms.newsletter-response', [
            'success' => true,
            'title' => 'Subscription Confirmed!',
            'message' => 'Thank you for confirming your subscription to Prayaag International School updates.',
        ]);
    }

    public function unsubscribe(Request $request, string $id)
    {
        if (!$request->hasValidSignature()) {
            return view('cms.newsletter-response', [
                'success' => false,
                'title' => 'Invalid Unsubscribe Link',
                'message' => 'The unsubscribe link signature is invalid or has expired.',
            ]);
        }

        $subscriber = NewsletterSubscriber::find($id);

        if ($subscriber) {
            $subscriber->update([
                'status' => 'unsubscribed',
                'unsubscribed_at' => now(),
            ]);
        }

        return view('cms.newsletter-response', [
            'success' => true,
            'title' => 'Unsubscribed Successfully',
            'message' => 'You have been unsubscribed from Prayaag International School emails.',
        ]);
    }
}
