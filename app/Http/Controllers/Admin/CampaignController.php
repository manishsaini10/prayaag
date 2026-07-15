<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\Enterprise\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::latest()->paginate(20);
        return view('chatbot.admin.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        return view('chatbot.admin.campaigns.form', ['campaign' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:email,sms,push,in-app',
            'channel' => 'required|string|max:50',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'audience_filter' => 'nullable|array',
            'status' => 'nullable|in:draft,scheduled,sending,sent',
            'scheduled_at' => 'nullable|date|after:now',
            'targeting_rules' => 'nullable|array',
        ]);

        $campaign = Campaign::create($validated);

        return redirect()->route('admin.chatbot.campaigns.index')
            ->with('success', 'Campaign created successfully.');
    }

    public function edit($id)
    {
        $campaign = Campaign::findOrFail($id);
        return view('chatbot.admin.campaigns.form', compact('campaign'));
    }

    public function update(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:email,sms,push,in-app',
            'channel' => 'required|string|max:50',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'audience_filter' => 'nullable|array',
            'status' => 'nullable|in:draft,scheduled,sending,sent',
            'scheduled_at' => 'nullable|date',
            'targeting_rules' => 'nullable|array',
        ]);

        $campaign->update($validated);

        return redirect()->route('admin.chatbot.campaigns.index')
            ->with('success', 'Campaign updated successfully.');
    }

    public function destroy($id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->delete();

        return redirect()->route('admin.chatbot.campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }

    public function send($id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->update(['status' => 'sending']);

        // In production: queue campaign dispatch
        return redirect()->route('admin.chatbot.campaigns.index')
            ->with('success', 'Campaign sending initiated.');
    }

    public function duplicate($id)
    {
        $campaign = Campaign::findOrFail($id);
        $copy = $campaign->replicate(['status']);
        $copy->name = $campaign->name . ' (Copy)';
        $copy->status = 'draft';
        $copy->save();

        return redirect()->route('admin.chatbot.campaigns.index')
            ->with('success', 'Campaign duplicated successfully.');
    }
}
