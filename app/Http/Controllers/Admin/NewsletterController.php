<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendNewsletterCampaignJob;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    public function index(): View
    {
        $campaigns = NewsletterCampaign::latest()->paginate(15);
        $subscriberCount = NewsletterSubscriber::subscribed()->count();

        return view('admin.newsletter.campaigns.index', compact('campaigns', 'subscriberCount'));
    }

    public function create(): View
    {
        $subscriberCount = NewsletterSubscriber::subscribed()->count();
        return view('admin.newsletter.campaigns.create', compact('subscriberCount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
            'scheduled_at' => 'nullable|date',
        ]);

        $campaign = NewsletterCampaign::create([
            'subject' => $validated['subject'],
            'body_html' => $validated['body_html'],
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'status' => $request->has('send_now') ? 'sending' : ($request->filled('scheduled_at') ? 'scheduled' : 'draft'),
            'created_by' => auth()->id(),
        ]);

        if ($request->has('send_now')) {
            SendNewsletterCampaignJob::dispatch($campaign->id);
            return redirect()->route('admin.newsletter.campaigns.index')
                ->with('success', 'Newsletter campaign is now sending in the background!');
        }

        return redirect()->route('admin.newsletter.campaigns.index')
            ->with('success', 'Newsletter campaign saved as draft.');
    }

    public function edit(string $id): View
    {
        $campaign = NewsletterCampaign::findOrFail($id);
        return view('admin.newsletter.campaigns.edit', compact('campaign'));
    }

    public function update(Request $request, string $id)
    {
        $campaign = NewsletterCampaign::findOrFail($id);

        if (in_array($campaign->status, ['sending', 'sent'])) {
            return back()->with('error', 'Cannot edit a campaign that has already been sent or is currently sending.');
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
            'scheduled_at' => 'nullable|date',
        ]);

        $campaign->update([
            'subject' => $validated['subject'],
            'body_html' => $validated['body_html'],
            'scheduled_at' => $validated['scheduled_at'] ?? null,
        ]);

        return redirect()->route('admin.newsletter.campaigns.index')
            ->with('success', 'Campaign updated.');
    }

    public function destroy(string $id)
    {
        $campaign = NewsletterCampaign::findOrFail($id);
        $campaign->delete();

        return redirect()->route('admin.newsletter.campaigns.index')
            ->with('success', 'Campaign deleted.');
    }

    public function sendNow(string $id)
    {
        $campaign = NewsletterCampaign::findOrFail($id);

        SendNewsletterCampaignJob::dispatch($campaign->id);

        return back()->with('success', 'Campaign dispatch started.');
    }
}
