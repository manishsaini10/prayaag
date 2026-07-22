<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Core\Chatbot\Repositories\ChatbotRepository;
use App\Core\Chatbot\Services\ChatbotRAGService;
use App\Core\Chatbot\Services\CannedResponseService;
use App\Models\Chatbot\ChatbotKbDocument;
use App\Models\Chatbot\ChatbotLead;
use App\Models\Chatbot\ChatbotFlow;
use App\Models\Chatbot\ChatbotConversation;
use App\Models\Chatbot\CannedResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminChatbotController extends Controller
{
    public function __construct(
        private readonly ChatbotRepository $repository
    ) {}

    public function index()
    {
        $settings = $this->repository->getSettings();
        
        $stats = [
            'total_chats' => ChatbotConversation::count(),
            'open_chats' => ChatbotConversation::whereIn('status', ['open', 'pending'])->count(),
            'closed_chats' => ChatbotConversation::where('status', 'closed')->count(),
            'ai_responses' => ChatbotConversation::where('ai_handled', true)->count(),
            'leads_count' => ChatbotLead::count(),
        ];

        return view('admin.chatbot.index', compact('settings', 'stats'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'enable_chatbot' => 'boolean',
            'enable_ai' => 'boolean',
            'enable_live_agent' => 'boolean',
            'enable_offline_form' => 'boolean',
            'enable_kb' => 'boolean',
            'enable_visitor_tracking' => 'boolean',
            'enable_sound_notification' => 'boolean',
            'enable_typing_indicator' => 'boolean',
            'enable_read_receipts' => 'boolean',
            'enable_seen_status' => 'boolean',
            'enable_conversation_rating' => 'boolean',
            'enable_emoji' => 'boolean',
            'enable_dark_mode' => 'boolean',
            'enable_whatsapp_fallback' => 'boolean',
            'widget_position' => 'string|in:bottom-left,bottom-right',
            'widget_shape' => 'string|in:rounded,square,bubble',
            'launcher_style' => 'string|in:icon,avatar,button',
            'primary_color' => 'string|max:7',
            'secondary_color' => 'string|max:7',
            'custom_css' => 'nullable|string',
            'custom_js' => 'nullable|string',
            'settings_data.ai.provider' => 'string|in:gemini,openai,claude,openrouter,ollama,groq,huggingface,mistral,together,deepseek',
            'settings_data.ai.model' => 'string',
            'settings_data.ai.api_key' => 'nullable|string',
            'settings_data.ai.gemini_key' => 'nullable|string',
            'settings_data.ai.openai_key' => 'nullable|string',
            'settings_data.ai.groq_key' => 'nullable|string',
            'settings_data.ai.huggingface_key' => 'nullable|string',
            'settings_data.ai.mistral_key' => 'nullable|string',
            'settings_data.ai.temperature' => 'numeric',
            'settings_data.ai.max_tokens' => 'integer',
            'settings_data.whatsapp.phone' => 'nullable|string',
            'settings_data.greetings.welcome' => 'nullable|string',
        ]);

        $checkboxes = [
            'enable_chatbot', 'enable_ai', 'enable_live_agent', 'enable_offline_form', 
            'enable_kb', 'enable_visitor_tracking', 'enable_sound_notification', 
            'enable_typing_indicator', 'enable_read_receipts', 'enable_seen_status', 
            'enable_conversation_rating', 'enable_emoji', 'enable_dark_mode', 'enable_whatsapp_fallback'
        ];
        foreach ($checkboxes as $box) {
            $data[$box] = $request->has($box);
        }

        $this->repository->updateSettings($data);

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public function conversations()
    {
        $conversations = $this->repository->getActiveConversations();
        $agents = \App\Models\User::orderBy('name')->get(['id', 'name', 'email']);
        return view('admin.chatbot.conversations', compact('conversations', 'agents'));
    }

    public function listJson()
    {
        $conversations = $this->repository->getActiveConversations();
        return response()->json($conversations->map(fn($c) => [
            'id' => $c->id,
            'status' => $c->status,
            'operator_id' => $c->operator_id,
            'updated_diff' => $c->updated_at->diffForHumans(null, true),
            'visitor' => [
                'name' => $c->visitor->name ?? 'Visitor',
                'email' => $c->visitor->email ?? 'N/A',
                'device' => $c->visitor->device ?? 'Desktop',
                'browser' => $c->visitor->browser ?? 'Chrome',
                'ip_address' => $c->visitor->ip_address ?? '0.0.0.0',
                'country' => $c->visitor->country ?? 'India',
            ]
        ]));
    }

    public function getMessages($id)
    {
        $messages = $this->repository->getMessages($id);
        return response()->json($messages);
    }

    public function sendMessage(Request $request, $id)
    {
        $request->validate(['message' => 'required|string']);

        $message = $this->repository->createMessage(
            $id,
            'agent',
            Auth::id(),
            $request->input('message')
        );

        $convo = ChatbotConversation::find($id);
        if ($convo) {
            $convo->update([
                'status' => 'open',
                'ai_handled' => false,
                'operator_id' => Auth::id(),
            ]);
        }

        return response()->json($message);
    }

    public function updateConversationStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:open,closed,pending']);
        $convo = ChatbotConversation::findOrFail($id);
        $convo->update(['status' => $request->input('status')]);
        return response()->json(['success' => true]);
    }

    public function assignConversation(Request $request, $id)
    {
        $request->validate([
            'operator_id' => 'nullable|exists:users,id',
        ]);
        $convo = ChatbotConversation::findOrFail($id);
        $convo->update([
            'operator_id' => $request->input('operator_id'),
            'ai_handled' => $request->input('operator_id') ? false : $convo->ai_handled,
        ]);
        return response()->json(['success' => true]);
    }

    public function kb()
    {
        $documents = ChatbotKbDocument::withCount('chunks')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.chatbot.kb', compact('documents'));
    }

    public function indexCms(ChatbotRAGService $ragService)
    {
        $ragService->indexCmsContent();
        return redirect()->back()->with('success', 'CMS Pages, blogs and events indexed successfully!');
    }

    public function uploadDoc(Request $request, ChatbotRAGService $ragService)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'doc_type' => 'required|string|in:faq,policy,general',
            'text_content' => 'required|string',
        ]);

        $ragService->saveKbDocument(
            uniqid('custom-'),
            $request->input('title'),
            $request->input('doc_type'),
            $request->input('text_content')
        );

        return redirect()->back()->with('success', 'Document indexed successfully!');
    }

    public function deleteDoc($id)
    {
        $doc = ChatbotKbDocument::findOrFail($id);
        $doc->delete();
        return redirect()->back()->with('success', 'Document deleted.');
    }

    public function leads()
    {
        $leads = ChatbotLead::with('visitor')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.chatbot.leads', compact('leads'));
    }

    public function flows()
    {
        $flow = ChatbotFlow::where('is_active', true)->first() ?? ChatbotFlow::first();
        return view('admin.chatbot.flows', compact('flow'));
    }

    public function saveFlow(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'flow_data' => 'required|json',
        ]);

        $flow = ChatbotFlow::updateOrCreate(
            ['is_active' => true],
            [
                'name' => $request->input('name'),
                'flow_data' => json_decode($request->input('flow_data'), true),
            ]
        );

        return redirect()->back()->with('success', 'Flow builder configuration saved!');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CANNED RESPONSES
    // ─────────────────────────────────────────────────────────────────────────

    public function cannedResponses(CannedResponseService $service)
    {
        $responses  = CannedResponse::orderBy('category')->orderBy('shortcut')->get();
        $categories = CannedResponse::categories();
        return view('admin.chatbot.canned', compact('responses', 'categories'));
    }

    public function storeCanned(Request $request, CannedResponseService $service)
    {
        $data = $request->validate([
            'shortcut' => 'required|string|max:50',
            'body'     => 'required|string',
            'category' => 'nullable|string|max:50',
        ]);
        $data['created_by'] = Auth::id();
        $service->create($data);
        return response()->json(['success' => true, 'message' => 'Canned response created.']);
    }

    public function updateCanned(Request $request, CannedResponseService $service, string $id)
    {
        $cr   = CannedResponse::findOrFail($id);
        $data = $request->validate([
            'shortcut' => 'sometimes|string|max:50',
            'body'     => 'sometimes|string',
            'category' => 'nullable|string|max:50',
        ]);
        $service->update($cr, $data);
        return response()->json(['success' => true, 'message' => 'Updated successfully.']);
    }

    public function destroyCanned(CannedResponseService $service, string $id)
    {
        $cr = CannedResponse::findOrFail($id);
        $service->delete($cr);
        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }

    /** AJAX: suggest canned responses based on user message */
    public function suggestCanned(Request $request, CannedResponseService $service)
    {
        $query  = $request->input('q', '');
        $intent = $request->input('intent', 'general');

        if (strlen($query) >= 1 && str_starts_with(trim($query), '/')) {
            // Slash search: return by shortcut/body match
            $results = $service->search(ltrim($query, '/'));
        } else {
            // AI-style suggestion by user message content
            $results = $service->suggest($query, $intent);
        }

        return response()->json($results->map(fn($r) => [
            'id'       => $r->id,
            'shortcut' => $r->shortcut,
            'body'     => $r->body,
            'category' => $r->category,
        ])->values());
    }

    public function assistantConfig()
    {
        $settings = $this->repository->getSettings();
        return view('admin.chatbot.assistant-config', compact('settings'));
    }

    public function saveAssistantConfig(Request $request)
    {
        $settings = $this->repository->getSettings();
        $settingsData = $settings->settings_data ?? [];

        $assistants = [
            'enable_admission'   => $request->boolean('settings_data.assistants.enable_admission'),
            'enable_job'         => $request->boolean('settings_data.assistants.enable_job'),
            'admission_greeting' => $request->input('settings_data.assistants.admission_greeting'),
            'job_greeting'       => $request->input('settings_data.assistants.job_greeting'),
        ];

        $settingsData['assistants'] = $assistants;

        $settings->update([
            'settings_data' => $settingsData
        ]);

        return redirect()->back()->with('success', 'Conversational Assistant settings updated successfully.');
    }
}
