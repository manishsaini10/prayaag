<?php

namespace App\Core\Chatbot\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\Enterprise\Ticket;
use App\Models\Chatbot\Enterprise\TicketReply;
use App\Models\Chatbot\Enterprise\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('chatbot.tickets.view');
        $query = Ticket::with(['department', 'assignedAgent', 'contact']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $tickets = $query->latest()->paginate(20);
        $departments = Department::orderBy('name')->get();

        if ($request->wantsJson()) {
            return response()->json($tickets);
        }

        return view('chatbot.admin.tickets.index', compact('tickets', 'departments'));
    }

    public function create()
    {
        Gate::authorize('chatbot.tickets.create');
        $departments = Department::orderBy('name')->get();
        $agents = User::all();
        return view('chatbot.admin.tickets.form', compact('departments', 'agents'));
    }

    public function store(Request $request)
    {
        Gate::authorize('chatbot.tickets.create');
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:open,pending,resolved,closed,on-hold',
            'priority' => 'nullable|string|in:low,medium,high,urgent,critical',
            'category' => 'nullable|string|max:30',
            'department_id' => 'nullable|exists:chatbot_departments,id',
            'assigned_agent_id' => 'nullable|exists:users,id',
            'source' => 'nullable|string|max:30',
            'channel' => 'nullable|string|max:30',
            'contact_id' => 'nullable|exists:chatbot_contacts,id',
            'conversation_id' => 'nullable|exists:chatbot_conversations,id',
            'visitor_id' => 'nullable|exists:chatbot_visitors,id',
        ]);

        $ticket = Ticket::create($data);

        if ($request->wantsJson()) {
            return response()->json($ticket, 201);
        }

        return redirect()->route('admin.chatbot.tickets.index')
            ->with('success', 'Ticket created successfully.');
    }

    public function show(Ticket $ticket)
    {
        Gate::authorize('chatbot.tickets.view');
        $ticket->load(['replies.user', 'department', 'assignedAgent', 'contact']);

        if (request()->wantsJson()) {
            return response()->json($ticket);
        }

        return view('chatbot.admin.tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        Gate::authorize('chatbot.tickets.update');
        $departments = Department::orderBy('name')->get();
        $agents = User::all();
        return view('chatbot.admin.tickets.form', compact('ticket', 'departments', 'agents'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        Gate::authorize('chatbot.tickets.update');
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:open,pending,resolved,closed,on-hold',
            'priority' => 'nullable|string|in:low,medium,high,urgent,critical',
            'category' => 'nullable|string|max:30',
            'department_id' => 'nullable|exists:chatbot_departments,id',
            'assigned_agent_id' => 'nullable|exists:users,id',
            'source' => 'nullable|string|max:30',
            'channel' => 'nullable|string|max:30',
            'contact_id' => 'nullable|exists:chatbot_contacts,id',
        ]);

        $ticket->update($data);

        if ($request->wantsJson()) {
            return response()->json($ticket);
        }

        return redirect()->route('admin.chatbot.tickets.index')
            ->with('success', 'Ticket updated successfully.');
    }

    public function destroy(Ticket $ticket)
    {
        Gate::authorize('chatbot.tickets.delete');
        $ticket->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Ticket deleted successfully.']);
        }

        return redirect()->route('admin.chatbot.tickets.index')
            ->with('success', 'Ticket deleted successfully.');
    }

    public function reply(Request $request, Ticket $ticket)
    {
        Gate::authorize('chatbot.tickets.reply');
        $data = $request->validate([
            'body' => 'required|string',
            'is_internal' => 'boolean',
            'is_solution' => 'boolean',
        ]);

        $reply = $ticket->replies()->create([
            'user_id' => auth()->id(),
            'replier_type' => 'agent',
            'body' => $data['body'],
            'is_internal' => $data['is_internal'] ?? false,
            'is_solution' => $data['is_solution'] ?? false,
        ]);

        if ($request->wantsJson()) {
            return response()->json($reply, 201);
        }

        return back()->with('success', 'Reply added successfully.');
    }

    public function assign(Request $request, Ticket $ticket)
    {
        Gate::authorize('chatbot.tickets.assign');
        $data = $request->validate([
            'assigned_agent_id' => 'nullable|exists:users,id',
            'assigned_team_id' => 'nullable|exists:chatbot_teams,id',
        ]);

        $ticket->update($data);

        if ($request->wantsJson()) {
            return response()->json($ticket);
        }

        return back()->with('success', 'Ticket assigned successfully.');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        Gate::authorize('chatbot.tickets.status');
        $data = $request->validate([
            'status' => 'required|string|in:open,pending,resolved,closed,on-hold',
        ]);

        $ticket->update($data);

        if ($request->wantsJson()) {
            return response()->json($ticket);
        }

        return back()->with('success', 'Ticket status updated successfully.');
    }
}
