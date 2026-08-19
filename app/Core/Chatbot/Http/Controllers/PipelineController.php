<?php

namespace App\Core\Chatbot\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\Enterprise\Pipeline;
use App\Models\Chatbot\Enterprise\PipelineStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PipelineController extends Controller
{
    public function index()
    {
        Gate::authorize('chatbot.contacts.view');

        $pipelines = Pipeline::with(['stages' => fn($q) => $q->orderBy('sort_order')])
            ->withCount('deals')
            ->orderBy('sort_order')
            ->get();

        return view('chatbot.admin.pipelines.index', compact('pipelines'));
    }

    public function create()
    {
        Gate::authorize('chatbot.contacts.create');
        return view('chatbot.admin.pipelines.index', ['pipelines' => Pipeline::with(['stages' => fn($q) => $q->orderBy('sort_order')])->withCount('deals')->orderBy('sort_order')->get(), 'showCreateModal' => true]);
    }

    public function edit(Pipeline $pipeline)
    {
        Gate::authorize('chatbot.contacts.update');
        return view('chatbot.admin.pipelines.show', compact('pipeline'));
    }

    public function store(Request $request)
    {
        Gate::authorize('chatbot.contacts.create');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        Pipeline::create($data);

        return back()->with('success', 'Pipeline created successfully.');
    }

    public function show(Pipeline $pipeline)
    {
        Gate::authorize('chatbot.contacts.view');

        $pipeline->load(['stages' => fn($q) => $q->orderBy('sort_order'), 'deals.contact', 'deals.company']);

        return view('chatbot.admin.pipelines.show', compact('pipeline'));
    }

    public function update(Request $request, Pipeline $pipeline)
    {
        Gate::authorize('chatbot.contacts.update');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $pipeline->update($data);

        return back()->with('success', 'Pipeline updated successfully.');
    }

    public function destroy(Pipeline $pipeline)
    {
        Gate::authorize('chatbot.contacts.delete');

        $pipeline->delete();

        return redirect()->route('admin.chatbot.pipelines.index')
            ->with('success', 'Pipeline deleted successfully.');
    }

    public function storeStage(Request $request, Pipeline $pipeline)
    {
        Gate::authorize('chatbot.contacts.update');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'probability' => 'nullable|integer|min:0|max:100',
        ]);

        $data['sort_order'] = $pipeline->stages()->max('sort_order') + 1;

        $pipeline->stages()->create($data);

        return back()->with('success', 'Stage added successfully.');
    }

    public function updateStage(Request $request, Pipeline $pipeline, PipelineStage $stage)
    {
        Gate::authorize('chatbot.contacts.update');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'probability' => 'nullable|integer|min:0|max:100',
        ]);

        $stage->update($data);

        return back()->with('success', 'Stage updated successfully.');
    }

    public function reorderStages(Request $request, Pipeline $pipeline)
    {
        Gate::authorize('chatbot.contacts.update');

        $request->validate(['stages' => 'required|array']);
        $request->validate(['stages.*.id' => 'exists:chatbot_pipeline_stages,id']);
        $request->validate(['stages.*.sort_order' => 'integer|min:0']);

        foreach ($request->stages as $s) {
            PipelineStage::where('id', $s['id'])
                ->where('pipeline_id', $pipeline->id)
                ->update(['sort_order' => $s['sort_order']]);
        }

        return response()->json(['success' => true]);
    }

    public function destroyStage(Pipeline $pipeline, PipelineStage $stage)
    {
        Gate::authorize('chatbot.contacts.delete');

        $stage->delete();

        return back()->with('success', 'Stage deleted successfully.');
    }
}
