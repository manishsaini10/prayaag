<?php

namespace App\Core\Chatbot\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\Enterprise\CannedResponse;
use App\Models\Chatbot\Enterprise\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CannedResponseController extends Controller
{
    public function index()
    {
        Gate::authorize('chatbot.tickets.view');

        $responses = CannedResponse::with(['createdBy', 'department'])
            ->orderBy('shortcut')
            ->get();

        $departments = Department::orderBy('name')->get();

        return view('chatbot.admin.tickets.canned', compact('responses', 'departments'));
    }

    public function store(Request $request)
    {
        Gate::authorize('chatbot.tickets.create');

        $data = $request->validate([
            'shortcut' => 'required|string|max:50',
            'body' => 'required|string',
            'category' => 'nullable|string|max:50',
            'department_id' => 'nullable|exists:chatbot_departments,id',
        ]);

        $data['created_by'] = auth()->id();

        CannedResponse::create($data);

        return back()->with('success', 'Canned response created successfully.');
    }

    public function update(Request $request, CannedResponse $cannedResponse)
    {
        Gate::authorize('chatbot.tickets.update');

        $data = $request->validate([
            'shortcut' => 'required|string|max:50',
            'body' => 'required|string',
            'category' => 'nullable|string|max:50',
            'department_id' => 'nullable|exists:chatbot_departments,id',
        ]);

        $cannedResponse->update($data);

        return back()->with('success', 'Canned response updated successfully.');
    }

    public function destroy(CannedResponse $cannedResponse)
    {
        Gate::authorize('chatbot.tickets.delete');

        $cannedResponse->delete();

        return back()->with('success', 'Canned response deleted successfully.');
    }
}
