<?php

namespace App\Core\Chatbot\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\ChatbotVisitor;
use App\Models\Chatbot\Enterprise\VisitorSession;
use App\Models\Chatbot\Enterprise\VisitorPage;
use App\Models\Chatbot\Enterprise\VisitorEvent;
use App\Core\Chatbot\Services\VisitorTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VisitorTrackingAdminController extends Controller
{
    public function __construct(
        private readonly VisitorTrackingService $trackingService
    ) {}

    public function index()
    {
        Gate::authorize('chatbot.kb.view');

        $stats = $this->trackingService->getAdminStats();
        $live = $this->trackingService->getOnlineVisitors();

        $visitors = ChatbotVisitor::withCount('sessions')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('chatbot.admin.visitors.index', compact('stats', 'live', 'visitors'));
    }

    public function show(ChatbotVisitor $visitor)
    {
        Gate::authorize('chatbot.kb.view');

        $visitor->loadMissing(['sessions.pages', 'sessions.events', 'conversations', 'leads']);
        $timeline = $this->trackingService->getVisitorTimeline($visitor);

        return view('chatbot.admin.visitors.show', compact('visitor', 'timeline'));
    }

    public function live()
    {
        Gate::authorize('chatbot.kb.view');

        $live = $this->trackingService->getOnlineVisitors();

        return view('chatbot.admin.visitors.live', compact('live'));
    }

    public function liveData()
    {
        Gate::authorize('chatbot.kb.view');

        $live = $this->trackingService->getOnlineVisitors();

        return response()->json($live);
    }

    public function deleteVisitor(ChatbotVisitor $visitor)
    {
        Gate::authorize('chatbot.kb.delete');

        $visitor->delete();

        return redirect()->route('admin.chatbot.visitors.index')
            ->with('success', 'Visitor deleted successfully.');
    }
}
