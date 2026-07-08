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

        Subscriber::updateOrCreate(
            ['email' => $data['email']],
            [
                'name'            => $data['name'] ?? null,
                'source'          => $data['source'] ?? null,
                'status'          => 'subscribed',
                'subscribed_at'   => now(),
                'unsubscribed_at' => null,
            ]
        );

        return back()->with('subscribed', true);
    }
}
