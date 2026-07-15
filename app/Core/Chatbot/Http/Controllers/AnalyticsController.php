<?php

namespace App\Core\Chatbot\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\Enterprise\Report;
use App\Models\Chatbot\ChatbotConversation;
use App\Models\Chatbot\ChatbotMessage;
use App\Models\Chatbot\ChatbotLead;
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
        $openTickets = Ticket::whereIn('status', ['open', 'pending'])->count();
        $activeAgents = AgentStatus::where('status', 'online')->count();

        $now = now();
        $todayStart = $now->copy()->startOfDay();
        $weekStart = $now->copy()->startOfWeek();
        $monthStart = $now->copy()->startOfMonth();

        $metrics = [
            ['label' => 'Conversations', 'today' => ChatbotConversation::where('created_at', '>=', $todayStart)->count(), 'week' => ChatbotConversation::where('created_at', '>=', $weekStart)->count(), 'month' => ChatbotConversation::where('created_at', '>=', $monthStart)->count(), 'all' => $totalConversations],
            ['label' => 'Messages', 'today' => ChatbotMessage::where('created_at', '>=', $todayStart)->count(), 'week' => ChatbotMessage::where('created_at', '>=', $weekStart)->count(), 'month' => ChatbotMessage::where('created_at', '>=', $monthStart)->count(), 'all' => $totalMessages],
            ['label' => 'Leads', 'today' => ChatbotLead::where('created_at', '>=', $todayStart)->count(), 'week' => ChatbotLead::where('created_at', '>=', $weekStart)->count(), 'month' => ChatbotLead::where('created_at', '>=', $monthStart)->count(), 'all' => ChatbotLead::count()],
            ['label' => 'Tickets', 'today' => Ticket::where('created_at', '>=', $todayStart)->count(), 'week' => Ticket::where('created_at', '>=', $weekStart)->count(), 'month' => Ticket::where('created_at', '>=', $monthStart)->count(), 'all' => Ticket::count()],
        ];

        if (request()->wantsJson()) {
            return response()->json(compact(
                'totalConversations', 'totalMessages', 'openTickets', 'activeAgents', 'metrics'
            ));
        }

        return view('chatbot.admin.analytics.index', compact(
            'totalConversations', 'totalMessages', 'openTickets', 'activeAgents', 'metrics'
        ));
    }

    public function realtime()
    {
        Gate::authorize('chatbot.analytics.view');
        $activeConversations = ChatbotConversation::where('status', 'active')->count();
        $messagesLastHour = ChatbotMessage::where('created_at', '>=', now()->subHour())->count();
        $activeAgents = AgentStatus::where('status', 'online')->count();
        $pendingTickets = Ticket::where('status', 'open')->count();

        $days = collect(range(29, 0))->map(function ($i) {
            $date = now()->subDays($i);
            return [
                'label' => $date->format('M d'),
                'value' => ChatbotConversation::whereDate('created_at', $date)->count(),
            ];
        });

        return response()->json([
            'activeConversations' => $activeConversations,
            'messagesLastHour' => $messagesLastHour,
            'activeAgents' => $activeAgents,
            'pendingTickets' => $pendingTickets,
            'labels' => $days->pluck('label'),
            'values' => $days->pluck('value'),
        ]);
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
            'type' => 'required|string|max:30',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $report = Report::create([
            'type' => $data['type'],
            'name' => $data['type'] . ' Report',
            'date_from' => $data['date_from'] ?? null,
            'date_to' => $data['date_to'] ?? null,
            'config' => json_encode([]),
            'created_by' => auth()->id(),
        ]);

        if ($request->wantsJson()) {
            return response()->json($report, 201);
        }

        return redirect()->route('admin.chatbot.analytics.reports')
            ->with('success', 'Report generated successfully.');
    }
}
