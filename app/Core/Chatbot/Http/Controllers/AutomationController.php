<?php

namespace App\Core\Chatbot\Http\Controllers;

use App\Core\Chatbot\Services\AutomationService;
use App\Http\Controllers\Controller;
use App\Models\Chatbot\Enterprise\Automation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AutomationController extends Controller
{
    public function __construct(
        protected AutomationService $automationService
    ) {}

    public function index(Request $request)
    {
        Gate::authorize('chatbot.automations.view');
        $query = Automation::query();

        if ($request->filled('trigger_type')) {
            $query->where('trigger_type', $request->trigger_type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $automations = $query->latest()->paginate(20);
        $triggers = Automation::TRIGGERS;
        $statuses = Automation::STATUSES;

        if ($request->wantsJson()) {
            return response()->json($automations);
        }

        return view('chatbot.admin.automations.index', compact('automations', 'triggers', 'statuses'));
    }

    public function create()
    {
        Gate::authorize('chatbot.automations.create');
        $triggers = Automation::TRIGGERS;
        $actions = Automation::ACTIONS;
        $statuses = Automation::STATUSES;
        return view('chatbot.admin.automations.form', compact('triggers', 'actions', 'statuses'));
    }

    public function store(Request $request)
    {
        Gate::authorize('chatbot.automations.create');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trigger_type' => 'required|string|in:' . implode(',', Automation::TRIGGERS),
            'trigger_config' => 'nullable|json',
            'conditions' => 'nullable|json',
            'actions' => 'required|json',
            'schedule' => 'nullable|json',
            'priority' => 'nullable|integer|min:0',
            'max_executions' => 'nullable|integer|min:0',
            'status' => 'nullable|string|in:' . implode(',', Automation::STATUSES),
        ]);

        $data['created_by'] = auth()->id();
        Automation::create($data);

        return redirect()->route('admin.chatbot.automations.index')
            ->with('success', 'Automation created successfully.');
    }

    public function show(Automation $automation)
    {
        Gate::authorize('chatbot.automations.view');
        $logs = $automation->logs()->latest()->paginate(20);
        return view('chatbot.admin.automations.show', compact('automation', 'logs'));
    }

    public function edit(Automation $automation)
    {
        Gate::authorize('chatbot.automations.update');
        $triggers = Automation::TRIGGERS;
        $actions = Automation::ACTIONS;
        $statuses = Automation::STATUSES;
        return view('chatbot.admin.automations.form', compact('automation', 'triggers', 'actions', 'statuses'));
    }

    public function update(Request $request, Automation $automation)
    {
        Gate::authorize('chatbot.automations.update');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trigger_type' => 'required|string|in:' . implode(',', Automation::TRIGGERS),
            'trigger_config' => 'nullable|json',
            'conditions' => 'nullable|json',
            'actions' => 'required|json',
            'schedule' => 'nullable|json',
            'priority' => 'nullable|integer|min:0',
            'max_executions' => 'nullable|integer|min:0',
            'status' => 'nullable|string|in:' . implode(',', Automation::STATUSES),
        ]);

        $automation->update($data);

        if ($request->wantsJson()) {
            return response()->json($automation);
        }

        return redirect()->route('admin.chatbot.automations.index')
            ->with('success', 'Automation updated successfully.');
    }

    public function destroy(Automation $automation)
    {
        Gate::authorize('chatbot.automations.delete');
        $automation->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Automation deleted successfully.']);
        }

        return redirect()->route('admin.chatbot.automations.index')
            ->with('success', 'Automation deleted successfully.');
    }

    public function toggle(Automation $automation)
    {
        Gate::authorize('chatbot.automations.toggle');
        $automation->update([
            'is_active' => !$automation->is_active,
        ]);

        $status = $automation->fresh()->is_active ? 'activated' : 'deactivated';

        if (request()->wantsJson()) {
            return response()->json(['message' => "Automation {$status}.", 'is_active' => $automation->is_active]);
        }

        return back()->with('success', "Automation {$status} successfully.");
    }

    public function test(Automation $automation)
    {
        Gate::authorize('chatbot.automations.test');

        $this->automationService->execute($automation, 'manual_test', [
            'triggered_by' => auth()->user()?->name ?? 'system',
            'test' => true,
            'timestamp' => now()->toIso8601String(),
        ]);

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Automation test completed.', 'automation' => $automation->fresh()]);
        }

        return back()->with('success', 'Automation test triggered successfully.');
    }

    public function showJson($id)
    {
        $automation = Automation::with('logs')->findOrFail($id);
        return response()->json($automation);
    }
}
