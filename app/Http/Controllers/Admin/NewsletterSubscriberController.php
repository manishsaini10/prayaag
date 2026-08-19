<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsletterSubscriberController extends Controller
{
    public function index(Request $request): View
    {
        $query = NewsletterSubscriber::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $subscribers = $query->paginate(20)->withQueryString();

        return view('admin.newsletter.subscribers.index', compact('subscribers'));
    }

    public function unsubscribe(string $id)
    {
        $subscriber = NewsletterSubscriber::findOrFail($id);
        $subscriber->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);

        return back()->with('success', 'Subscriber manually unsubscribed.');
    }

    public function export()
    {
        $subscribers = NewsletterSubscriber::subscribed()->get();

        $csvData = "ID,Email,Status,Subscribed At\n";
        foreach ($subscribers as $sub) {
            $csvData .= "{$sub->id},{$sub->email},{$sub->status},{$sub->subscribed_at}\n";
        }

        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="newsletter_subscribers.csv"',
        ]);
    }
}
