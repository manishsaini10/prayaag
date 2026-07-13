<?php

namespace App\Core\Chatbot\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\Enterprise\Deal;
use App\Models\Chatbot\Enterprise\Pipeline;
use App\Models\Chatbot\Enterprise\PipelineStage;
use App\Models\Chatbot\Enterprise\Contact;
use App\Models\Chatbot\Enterprise\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DealController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('chatbot.contacts.view');

        $query = Deal::with(['contact', 'pipeline', 'stage', 'company']);

        if ($request->filled('pipeline_id')) {
            $query->where('pipeline_id', $request->pipeline_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhereHas('contact', fn($cq) => $cq->where('first_name', 'like', "%{$s}%")->orWhere('last_name', 'like', "%{$s}%"));
            });
        }

        $deals = $query->orderBy('created_at', 'desc')->paginate(20);
        $pipelines = Pipeline::where('is_active', true)->get();

        if ($request->wantsJson()) {
            return response()->json($deals);
        }

        return view('chatbot.admin.deals.index', compact('deals', 'pipelines'));
    }

    public function kanban()
    {
        Gate::authorize('chatbot.contacts.view');

        $pipelines = Pipeline::with(['stages' => fn($q) => $q->orderBy('sort_order')->with(['deals' => fn($dq) => $dq->with('contact')->orderBy('created_at', 'desc')])])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('chatbot.admin.deals.kanban', compact('pipelines'));
    }

    public function store(Request $request)
    {
        Gate::authorize('chatbot.contacts.create');

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'contact_id' => 'required|exists:chatbot_contacts,id',
            'company_id' => 'nullable|exists:chatbot_companies,id',
            'pipeline_id' => 'required|exists:chatbot_pipelines,id',
            'stage_id' => 'required|exists:chatbot_pipeline_stages,id',
            'value' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'expected_close_date' => 'nullable|date',
            'probability' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $stage = PipelineStage::findOrFail($data['stage_id']);
        $data['pipeline_id'] = $stage->pipeline_id;
        if (!isset($data['currency'])) $data['currency'] = 'INR';

        Deal::create($data);

        return redirect()->route('admin.chatbot.deals.index')
            ->with('success', 'Deal created successfully.');
    }

    public function show(Deal $deal)
    {
        Gate::authorize('chatbot.contacts.view');

        $deal->load(['contact', 'company', 'pipeline', 'stage']);

        return view('chatbot.admin.deals.show', compact('deal'));
    }

    public function update(Request $request, Deal $deal)
    {
        Gate::authorize('chatbot.contacts.update');

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'value' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'expected_close_date' => 'nullable|date',
            'stage_id' => 'required|exists:chatbot_pipeline_stages,id',
            'probability' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $stage = PipelineStage::findOrFail($data['stage_id']);
        $data['pipeline_id'] = $stage->pipeline_id;

        $deal->update($data);

        return redirect()->route('admin.chatbot.deals.show', $deal)
            ->with('success', 'Deal updated successfully.');
    }

    public function moveStage(Request $request, Deal $deal)
    {
        Gate::authorize('chatbot.contacts.update');

        $request->validate(['stage_id' => 'required|exists:chatbot_pipeline_stages,id']);

        $stage = PipelineStage::findOrFail($request->stage_id);
        $deal->update([
            'stage_id' => $stage->id,
            'pipeline_id' => $stage->pipeline_id,
        ]);

        return response()->json(['success' => true, 'deal' => $deal->fresh(['stage'])]);
    }

    public function updateStatus(Request $request, Deal $deal)
    {
        Gate::authorize('chatbot.contacts.update');

        $request->validate(['status' => 'required|in:open,won,lost']);

        $data = ['status' => $request->status];
        if ($request->status === 'won') $data['closed_date'] = now();
        if ($request->status === 'lost') {
            $data['closed_date'] = now();
            $data['lost_reason'] = $request->input('lost_reason');
        }

        $deal->update($data);

        return back()->with('success', 'Deal status updated successfully.');
    }

    public function destroy(Deal $deal)
    {
        Gate::authorize('chatbot.contacts.delete');

        $deal->delete();

        return redirect()->route('admin.chatbot.deals.index')
            ->with('success', 'Deal deleted successfully.');
    }
}
