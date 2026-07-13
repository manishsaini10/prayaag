<?php

namespace App\Core\Chatbot\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\Enterprise\Report;
use App\Models\Chatbot\ChatbotConversation;
use App\Models\Chatbot\ChatbotMessage;
use App\Models\Chatbot\Enterprise\Ticket;
use App\Models\Chatbot\Enterprise\AgentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AnalyticsController extends Controller
{
    public function index()
    {
        Gate::authorize('chatbot.analytics.view');
        $totalConversations = ChatbotConversation::count();
        $totalMessages = ChatbotMessage::count();
        $totalTickets = Ticket::count();
        $activeAgents = AgentStatus::where('status', 'online')->count();

        if (request()->wantsJson()) {
            return response()->json(compact(
                'totalConversations', 'totalMessages', 'totalTickets', 'activeAgents'
            ));
        }

        return view('chatbot.admin.analytics.index', compact(
            'totalConversations', 'totalMessages', 'totalTickets', 'activeAgents'
        ));
    }

    public function realtime()
    {
        Gate::authorize('chatbot.analytics.view');
        $activeConversations = ChatbotConversation::where('status', 'active')->count();
        $messagesLastHour = ChatbotMessage::where('created_at', '>=', now()->subHour())->count();
        $activeAgents = AgentStatus::where('status', 'online')->count();
        $pendingTickets = Ticket::where('status', 'open')->count();

        return response()->json(compact(
            'activeConversations', 'messagesLastHour', 'activeAgents', 'pendingTickets'
        ));
    }

    public function reports()
    {
        Gate::authorize('chatbot.analytics.reports');
        $reports = Report::latest()->paginate(20);

        if (request()->wantsJson()) {
            return response()->json($reports);
        }

        return view('chatbot.admin.analytics.reports', compact('reports'));
    }

    public function generateReport(Request $request)
    {
        Gate::authorize('chatbot.analytics.reports');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:30',
            'config' => 'required|json',
            'schedule' => 'nullable|string|max:30',
            'recipients' => 'nullable|json',
        ]);

        $data['created_by'] = auth()->id();
        $report = Report::create($data);

        if ($request->wantsJson()) {
            return response()->json($report, 201);
        }

        return redirect()->route('admin.chatbot.analytics.reports')
            ->with('success', 'Report generated successfully.');
    }
}
