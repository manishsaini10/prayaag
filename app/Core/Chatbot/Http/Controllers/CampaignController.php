<?php

namespace App\Core\Chatbot\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\Enterprise\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('chatbot.campaigns.view');
        $query = Campaign::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        $campaigns = $query->latest()->paginate(20);

        if ($request->wantsJson()) {
            return response()->json($campaigns);
        }

        return view('chatbot.admin.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        Gate::authorize('chatbot.campaigns.create');
        return view('chatbot.admin.campaigns.form');
    }

    public function store(Request $request)
    {
        Gate::authorize('chatbot.campaigns.create');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:30',
            'channel' => 'nullable|string|max:30',
            'content' => 'nullable|string',
            'targeting_rules' => 'nullable|json',
            'schedule' => 'nullable|json',
            'scheduled_at' => 'nullable|date',
            'status' => 'nullable|string|in:draft,scheduled,sending,sent,completed,cancelled,failed',
        ]);

        $data['created_by'] = auth()->id();
        Campaign::create($data);

        return redirect()->route('admin.chatbot.campaigns.index')
            ->with('success', 'Campaign created successfully.');
    }

    public function edit(Campaign $campaign)
    {
        Gate::authorize('chatbot.campaigns.update');
        return view('chatbot.admin.campaigns.form', compact('campaign'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        Gate::authorize('chatbot.campaigns.update');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:30',
            'channel' => 'nullable|string|max:30',
            'content' => 'nullable|string',
            'targeting_rules' => 'nullable|json',
            'schedule' => 'nullable|json',
            'scheduled_at' => 'nullable|date',
            'status' => 'nullable|string|in:draft,scheduled,sending,sent,completed,cancelled,failed',
        ]);

        $campaign->update($data);

        if ($request->wantsJson()) {
            return response()->json($campaign);
        }

        return redirect()->route('admin.chatbot.campaigns.index')
            ->with('success', 'Campaign updated successfully.');
    }

    public function destroy(Campaign $campaign)
    {
        Gate::authorize('chatbot.campaigns.delete');
        $campaign->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Campaign deleted successfully.']);
        }

        return redirect()->route('admin.chatbot.campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }

    public function send(Campaign $campaign)
    {
        Gate::authorize('chatbot.campaigns.send');
        $campaign->update([
            'status' => 'sending',
            'started_at' => now(),
        ]);

        if (request()->wantsJson()) {
            return response()->json($campaign);
        }

        return redirect()->route('admin.chatbot.campaigns.index')
            ->with('success', 'Campaign is being sent.');
    }

    public function duplicate(Campaign $campaign)
    {
        Gate::authorize('chatbot.campaigns.create');
        Gate::authorize('chatbot.campaigns.view');
        $clone = $campaign->replicate();
        $clone->name = $campaign->name . ' (Copy)';
        $clone->status = 'draft';
        $clone->started_at = null;
        $clone->completed_at = null;
        $clone->save();

        if (request()->wantsJson()) {
            return response()->json($clone, 201);
        }

        return redirect()->route('admin.chatbot.campaigns.index')
            ->with('success', 'Campaign duplicated successfully.');
    }
}
