<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Core\Chatbot\Events\MessageSent;
use App\Core\Chatbot\Events\VisitorTyping;
use App\Core\Chatbot\Events\MessageRead;
use App\Core\Chatbot\Repositories\ChatbotRepository;
use App\Core\Chatbot\Services\ChatbotAIService;
use App\Core\Chatbot\Services\ChatbotRAGService;
use App\Models\Chatbot\ChatbotFormField;
use App\Models\Chatbot\ChatbotLead;
use App\Models\Chatbot\ChatbotConversation;
use App\Models\Chatbot\ChatbotMessage;
use App\Models\Chatbot\Enterprise\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicChatbotController extends Controller
{
    public function __construct(
        private readonly ChatbotRepository $repository
    ) {}

    public function config()
    {
        $settings = $this->repository->getSettings();
        $data = $settings->toArray();
        $data['departments'] = Department::where('is_active', true)->orderBy('sort_order')->get(['id', 'name', 'description', 'color']);
        if ($settings->enable_departments) {
            $data['departments'] = Department::where('is_active', true)->orderBy('sort_order')->get(['id', 'name', 'description', 'color']);
        } else {
            $data['departments'] = [];
        }
        $data['realtime'] = [
            'broadcast_key' => config('broadcasting.connections.reverb.key'),
            'broadcast_driver' => config('broadcasting.default'),
            'ws_host' => config('broadcasting.connections.reverb.options.host', 'localhost'),
            'ws_port' => config('broadcasting.connections.reverb.options.port', 8080),
            'ws_scheme' => config('broadcasting.connections.reverb.options.scheme', 'http'),
        ];
        return response()->json($data);
    }

    public function formFields()
    {
        $fields = ChatbotFormField::active()->ordered()->get()->map(fn($f) => [
            'id' => $f->id,
            'label' => $f->label,
            'field_key' => $f->field_key,
            'field_type' => $f->field_type,
            'placeholder' => $f->placeholder,
            'options' => $f->options ?? [],
            'is_required' => $f->is_required,
        ]);

        return response()->json($fields);
    }

    public function init(Request $request)
    {
        $sessionId = $request->input('session_id') ?? uniqid('sess_', true);

        $ip = $request->ip();
        $agent = $request->userAgent();

        $device = 'Desktop';
        if (preg_match('/(android|iphone|ipad|mobile)/i', $agent)) {
            $device = 'Mobile';
        }
        $browser = 'Chrome';
        if (preg_match('/firefox/i', $agent)) { $browser = 'Firefox'; }
        elseif (preg_match('/safari/i', $agent) && !preg_match('/chrome/i', $agent)) { $browser = 'Safari'; }
        elseif (preg_match('/edge/i', $agent)) { $browser = 'Edge'; }

        $visitor = $this->repository->findOrCreateVisitor($sessionId, [
            'ip_address' => $ip,
            'device' => $device,
            'browser' => $browser,
            'landing_page' => $request->input('landing_page'),
            'referrer' => $request->input('referrer'),
            'utm_source' => $request->input('utm_source'),
            'utm_medium' => $request->input('utm_medium'),
            'utm_campaign' => $request->input('utm_campaign'),
        ]);

        $convo = $visitor->conversations()->whereIn('status', ['open', 'pending'])->first();
        if (!$convo) {
            $convo = $this->repository->createConversation($visitor->id);
        }

        $messages = $this->repository->getMessages($convo->id);

        return response()->json([
            'session_id' => $sessionId,
            'visitor' => $visitor,
            'conversation_id' => $convo->id,
            'messages' => $messages,
        ]);
    }

    public function send(Request $request, ChatbotAIService $aiService, ChatbotRAGService $ragService)
    {
        $request->validate([
            'conversation_id' => 'required|exists:chatbot_conversations,id',
            'message' => 'required|string',
        ]);

        $convoId = $request->input('conversation_id');
        $userInput = $request->input('message');

        $visitorMessage = $this->repository->createMessage($convoId, 'visitor', null, $userInput);
        MessageSent::dispatch($visitorMessage);

        $conversation = $this->repository->findConversationById($convoId);

        if (!$conversation->ai_handled) {
            return response()->json([
                'visitor_message' => $visitorMessage,
                'bot_message' => null,
            ]);
        }

        $settings = $this->repository->getSettings();

        // 1. Check if visitor wants to talk to a human agent
        $lowercaseMsg = strtolower($userInput);
        $wantsHuman = false;
        foreach (['human', 'operator', 'agent', 'admin', 'support', 'live chat', 'representative', 'staff'] as $keyword) {
            if (str_contains($lowercaseMsg, $keyword)) {
                $wantsHuman = true;
                break;
            }
        }

        if ($wantsHuman && $settings->enable_live_agent) {
            $conversation->update([
                'ai_handled' => false,
                'status' => 'open',
            ]);

            $botMessage = $this->repository->createMessage(
                $convoId,
                'chatbot',
                null,
                "Sure, I am connecting you to a live support representative now. Please wait..."
            );
            MessageSent::dispatch($botMessage);

            return response()->json([
                'visitor_message' => $visitorMessage,
                'bot_message' => $botMessage,
            ]);
        }

        // 2. Check if AI config is empty/offline
        $aiConfig = $settings->settings_data['ai'] ?? [];
        $provider = $aiConfig['provider'] ?? 'gemini';
        $apiKey = $aiConfig["{$provider}_key"] ?? $aiConfig['api_key'] ?? '';
        $aiOffline = (empty($apiKey) && $provider !== 'ollama');

        if ($aiOffline) {
            if ($settings->enable_live_agent) {
                $conversation->update([
                    'ai_handled' => false,
                    'status' => 'open',
                ]);

                $botMessage = $this->repository->createMessage(
                    $convoId,
                    'chatbot',
                    null,
                    "Connecting you to an online support representative. Please wait..."
                );
                MessageSent::dispatch($botMessage);

                return response()->json([
                    'visitor_message' => $visitorMessage,
                    'bot_message' => $botMessage,
                ]);
            }
        }

        $kbContext = '';
        if ($settings->enable_kb) {
            $chunks = $ragService->search($userInput, 3);
            if (!empty($chunks)) {
                $kbContext = "Here is relevant information about Prayaag School to help you answer:\n";
                foreach ($chunks as $chunk) {
                    $kbContext .= "- " . $chunk['text'] . "\n";
                }
            }
        }

        $systemPrompt = "You are a professional, helpful AI chatbot for Prayaag School. "
            . "Use the school knowledge base context to answer. If the answer is not in the context, "
            . "answer politely using general school helper knowledge or ask the user to provide their email/phone so we can contact them. "
            . "Keep answers short, friendly, and structure with bullet points if helpful.\n\n"
            . $kbContext;

        $botReply = $aiService->generateResponse($conversation, $userInput, $systemPrompt);

        $botMessage = $this->repository->createMessage($convoId, 'chatbot', null, $botReply);
        MessageSent::dispatch($botMessage);

        return response()->json([
            'visitor_message' => $visitorMessage,
            'bot_message' => $botMessage,
        ]);
    }

    public function submitLead(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string|max:255',
            'form_data' => 'nullable|array',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'class' => 'nullable|string|max:50',
            'interest' => 'nullable|string|max:255',
        ]);

        $dynamicFields = ChatbotFormField::active()->ordered()->get();

        $formData = $request->input('form_data', []);
        $errors = [];

        foreach ($dynamicFields as $field) {
            $value = $formData[$field->field_key] ?? $request->input($field->field_key, '');
            $formData[$field->field_key] = $value;

            if ($field->is_required && empty($value)) {
                $errors[$field->field_key] = "{$field->label} is required.";
            }
        }

        if (!empty($errors)) {
            return response()->json(['success' => false, 'errors' => $errors], 422);
        }

        $visitor = $this->repository->findOrCreateVisitor($request->input('session_id'));

        $visitorData = [];
        if (!empty($formData['name'])) $visitorData['name'] = $formData['name'];
        if (!empty($formData['email'])) $visitorData['email'] = $formData['email'];
        if (!empty($formData['phone'])) $visitorData['phone'] = $formData['phone'];
        if (!empty($visitorData)) {
            $visitor->update($visitorData);
        }

        $lead = ChatbotLead::create([
            'visitor_id' => $visitor->id,
            'name' => $formData['name'] ?? $request->input('name', ''),
            'email' => $formData['email'] ?? $request->input('email'),
            'phone' => $formData['phone'] ?? $request->input('phone'),
            'admission_class' => $formData['class'] ?? $request->input('class'),
            'interest' => $formData['interest'] ?? $request->input('interest') ?? 'Admission Inquiry',
            'form_data' => $formData,
            'status' => 'new',
            'source' => 'chatbot',
        ]);

        return response()->json([
            'success' => true,
            'lead' => $lead,
        ]);
    }

    public function getMessages($id)
    {
        $messages = $this->repository->getMessages($id);
        return response()->json($messages);
    }

    public function closeConversation($id)
    {
        $convo = ChatbotConversation::findOrFail($id);
        $convo->update(['status' => 'closed']);
        return response()->json(['success' => true]);
    }

    public function typing(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:chatbot_conversations,id',
        ]);

        VisitorTyping::dispatch($request->conversation_id);

        return response()->json(['success' => true]);
    }

    public function markRead(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:chatbot_conversations,id',
            'message_ids' => 'required|array',
            'message_ids.*' => 'exists:chatbot_messages,id',
        ]);

        ChatbotMessage::whereIn('id', $request->message_ids)
            ->where('conversation_id', $request->conversation_id)
            ->update(['is_read' => true]);

        MessageRead::dispatch($request->conversation_id, $request->message_ids);

        return response()->json(['success' => true]);
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt,csv,mp3,wav,ogg,mp4,webm|max:20480',
            'conversation_id' => 'required|exists:chatbot_conversations,id',
        ]);

        $file = $request->file('file');
        $path = $file->store('chatbot-uploads', 'public');

        $message = $this->repository->createMessage(
            $request->input('conversation_id'),
            'visitor',
            null,
            $file->getClientOriginalName(),
            'file',
            [
                'file_path' => $path,
                'file_url' => Storage::url($path),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'file_name' => $file->getClientOriginalName(),
            ]
        );

        MessageSent::dispatch($message);

        return response()->json(['message' => $message]);
    }
}
