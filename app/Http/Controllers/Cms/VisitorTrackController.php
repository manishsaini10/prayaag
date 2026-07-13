<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Core\Chatbot\Services\VisitorTrackingService;
use Illuminate\Http\Request;

class VisitorTrackController extends Controller
{
    public function __construct(
        private readonly VisitorTrackingService $trackingService
    ) {}

    public function identify(Request $request)
    {
        $result = $this->trackingService->identifyVisitor($request);
        return response()->json([
            'success' => true,
            'visitor_id' => $result['visitor']->id,
            'session_token' => $result['session']->session_token,
        ]);
    }

    public function pageView(Request $request)
    {
        $validated = $request->validate([
            'session_token' => 'required|string',
            'url' => 'required|string',
            'title' => 'nullable|string|max:255',
            'referrer' => 'nullable|string',
        ]);

        $session = \App\Models\Chatbot\Enterprise\VisitorSession::where('session_token', $validated['session_token'])->first();
        if (!$session) {
            return response()->json(['success' => false, 'error' => 'Session not found'], 404);
        }

        $this->trackingService->trackPageView($request, $session->visitor, $session);

        return response()->json(['success' => true]);
    }

    public function event(Request $request)
    {
        $validated = $request->validate([
            'session_token' => 'required|string',
            'event_type' => 'required|string|max:50',
            'event_category' => 'nullable|string|max:50',
            'event_label' => 'nullable|string|max:255',
            'event_value' => 'nullable|string',
        ]);

        $visitor = \App\Models\Chatbot\ChatbotVisitor::where('session_id', $validated['session_token'])->first();
        if (!$visitor) {
            return response()->json(['success' => false, 'error' => 'Visitor not found'], 404);
        }

        $session = \App\Models\Chatbot\Enterprise\VisitorSession::where('session_token', $validated['session_token'])->first();

        $this->trackingService->trackEvent($request, $visitor, $session);

        return response()->json(['success' => true]);
    }

    public function heartbeat(Request $request)
    {
        $validated = $request->validate([
            'session_token' => 'required|string',
        ]);

        $session = \App\Models\Chatbot\Enterprise\VisitorSession::where('session_token', $validated['session_token'])
            ->where('is_active', true)
            ->first();

        if (!$session) {
            return response()->json(['success' => false, 'error' => 'No active session'], 404);
        }

        $this->trackingService->heartbeat($request, $session->visitor, $session);

        return response()->json(['success' => true]);
    }

    public function endSession(Request $request)
    {
        $validated = $request->validate([
            'session_token' => 'required|string',
        ]);

        $session = \App\Models\Chatbot\Enterprise\VisitorSession::where('session_token', $validated['session_token'])->first();
        if (!$session) {
            return response()->json(['success' => false], 404);
        }

        $this->trackingService->endSession($session);

        return response()->json(['success' => true]);
    }
}
