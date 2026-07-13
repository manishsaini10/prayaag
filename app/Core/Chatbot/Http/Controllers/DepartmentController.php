<?php

namespace App\Core\Chatbot\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\Enterprise\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DepartmentController extends Controller
{
    public function index()
    {
        Gate::authorize('chatbot.departments.view');
        $departments = Department::withCount('agents')->orderBy('sort_order')->get();
        return view('chatbot.admin.departments.index', compact('departments'));
    }

    public function create()
    {
        Gate::authorize('chatbot.departments.create');
        return view('chatbot.admin.departments.form');
    }

    public function store(Request $request)
    {
        Gate::authorize('chatbot.departments.create');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'email' => 'nullable|email',
            'priority' => 'nullable|string|in:low,medium,high',
            'is_active' => 'boolean',
        ]);

        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        Department::create($data);

        return redirect()->route('departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function edit(Department $department)
    {
        Gate::authorize('chatbot.departments.update');
        return view('chatbot.admin.departments.form', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        Gate::authorize('chatbot.departments.update');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'email' => 'nullable|email',
            'priority' => 'nullable|string|in:low,medium,high',
            'is_active' => 'boolean',
        ]);

        $department->update($data);
        return redirect()->route('departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        Gate::authorize('chatbot.departments.delete');
        $department->delete();
        return redirect()->route('departments.index')
            ->with('success', 'Department deleted successfully.');
    }

    public function assignAgent(Request $request, Department $department)
    {
        Gate::authorize('chatbot.departments.assign');
        $request->validate([
            'agent_id' => 'required|exists:users,id',
            'is_lead' => 'boolean',
            'max_concurrent_chats' => 'integer|min:1|max:20',
        ]);

        $department->agents()->syncWithoutDetaching([
            $request->agent_id => [
                'is_lead' => $request->boolean('is_lead'),
                'max_concurrent_chats' => $request->input('max_concurrent_chats', 5),
            ]
        ]);

        return back()->with('success', 'Agent assigned to department.');
    }

    public function removeAgent(Department $department, User $agent)
    {
        Gate::authorize('chatbot.departments.assign');
        $department->agents()->detach($agent);
        return back()->with('success', 'Agent removed from department.');
    }
}
