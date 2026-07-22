<?php

namespace App\Core\Chatbot\Services;

use App\Models\Chatbot\ChatbotConversation;
use App\Models\Chatbot\ChatbotSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotAIService
{
    private const API_URLS = [
        'openai'      => 'https://api.openai.com/v1/chat/completions',
        'openrouter'  => 'https://openrouter.ai/api/v1/chat/completions',
        'groq'        => 'https://api.groq.com/openai/v1/chat/completions',
        'mistral'     => 'https://api.mistral.ai/v1/chat/completions',
        'together'    => 'https://api.together.xyz/v1/chat/completions',
        'deepseek'    => 'https://api.deepseek.com/v1/chat/completions',
    ];

    public function __construct(
        private readonly MultiLLMRouter $router,
        private readonly ConfidenceScorer $scorer,
        private readonly ConversationMemory $memory,
    ) {}

    public static function make(): self
    {
        return new self(new MultiLLMRouter, new ConfidenceScorer, new ConversationMemory);
    }

    public function generateResponse(ChatbotConversation $conversation, string $userInput, string $systemPrompt = ''): string
    {
        $startTime = microtime(true);
        $settings = ChatbotSetting::first();
        $aiConfig = $settings?->settings_data['ai'] ?? [];

        $intent = $this->router->detectIntent($userInput);
        $conversation->update(['intent' => $intent]);

        $modelConfig = $this->router->getModelConfig($intent);
        $provider = $aiConfig['provider'] ?? $modelConfig['provider'];
        $model = $aiConfig['model'] ?? $modelConfig['model'];
        $apiKey = $aiConfig["{$provider}_key"] ?? $aiConfig['api_key'] ?? '';
        $temperature = (float) ($aiConfig['temperature'] ?? $modelConfig['temperature']);
        $maxTokens = (int) ($aiConfig['max_tokens'] ?? 1000);

        if (empty($apiKey) && $provider !== 'ollama') {
            return "Support Agent offline. Please contact us via email.";
        }

        $this->memory->summarize($conversation);
        $context = $this->memory->buildContext($conversation);

        if (empty($systemPrompt)) {
            $systemPrompt = $this->router->getSystemPrompt($intent, '');
        }

        $history = [
            ['role' => 'user', 'content' => $context . "\n\nUser's new message: " . $userInput],
        ];

        $responseContent = '';
        $needsHuman = false;
        $confidence = ['score' => 1.0, 'level' => 'high', 'needs_human' => false];

        try {
            $responseContent = match ($provider) {
                'gemini' => $this->callGemini($apiKey, $model, $history, $systemPrompt, $temperature, $maxTokens),
                'openai', 'openrouter', 'groq', 'mistral', 'together', 'deepseek' => $this->callOpenAI($apiKey, $model, $history, $systemPrompt, $temperature, $maxTokens, self::API_URLS[$provider]),
                'claude' => $this->callClaude($apiKey, $model, $history, $systemPrompt, $temperature, $maxTokens),
                'huggingface' => $this->callHuggingFace($apiKey, $model, $history, $systemPrompt, $temperature, $maxTokens),
                'ollama' => $this->callOllama($model, $history, $systemPrompt, $temperature, $maxTokens),
                default => $this->callGemini($apiKey, $model, $history, $systemPrompt, $temperature, $maxTokens),
            };

            $confidence = $this->scorer->score($responseContent, $intent);
            $needsHuman = $confidence['needs_human'];

            if ($confidence['level'] === 'low' && $needsHuman) {
                $settings = ChatbotSetting::first();
                if ($settings && $settings->enable_live_agent) {
                    $conversation->update([
                        'ai_handled' => false,
                        'status' => 'open',
                    ]);
                    $responseContent .= "\n\nI've connected you with a human agent who can better assist you with this.";
                }
            }
        } catch (\Exception $e) {
            Log::error('Chatbot AI service error: ' . $e->getMessage());

            $fallbackProviders = ['gemini', 'openai', 'groq', 'mistral', 'deepseek', 'together', 'claude'];
            $fallbackProviders = array_values(array_filter($fallbackProviders, fn($p) => $p !== $provider));
            $responseContent = null;

            foreach ($fallbackProviders as $fallbackProvider) {
                try {
                    $fallbackKey = $aiConfig[$fallbackProvider . '_key'] ?? null;
                    if (empty($fallbackKey)) continue;

                    $responseContent = match ($fallbackProvider) {
                        'gemini' => $this->callGemini($fallbackKey, $model, $history, $systemPrompt, $temperature, $maxTokens),
                        'openai', 'openrouter', 'groq', 'mistral', 'together', 'deepseek' => $this->callOpenAI($fallbackKey, $model, $history, $systemPrompt, $temperature, $maxTokens, self::API_URLS[$fallbackProvider]),
                        'claude' => $this->callClaude($fallbackKey, $model, $history, $systemPrompt, $temperature, $maxTokens),
                        'huggingface' => $this->callHuggingFace($fallbackKey, $model, $history, $systemPrompt, $temperature, $maxTokens),
                        default => null,
                    };
                    if ($responseContent) {
                        $confidence = $this->scorer->score($responseContent, $intent);
                        Log::info('Chatbot AI fallback succeeded: ' . $fallbackProvider);
                        break;
                    }
                } catch (\Exception $e2) {
                    Log::warning("Chatbot AI fallback {$fallbackProvider} failed: " . $e2->getMessage());
                    continue;
                }
            }

            if (!$responseContent) {
                Log::warning('All AI providers failed or are unconfigured. Falling back to local keyword responses.');
                $responseContent = $this->getLocalFallbackResponse($intent);
                $confidence = ['score' => 1.0, 'level' => 'high', 'needs_human' => false];
            }
        }

        $duration = (int) round((microtime(true) - $startTime) * 1000);
        $conversation->update([
            'response_time' => $duration,
            'token_usage' => $conversation->token_usage + strlen($userInput) / 4 + strlen($responseContent) / 4,
        ]);

        if ($needsHuman && $confidence['level'] === 'low') {
            $conversation->update(['ai_handled' => false]);
        }

        return $responseContent;
    }

    /**
     * Local rule-based fallback responses matching detected intents.
     */
    protected function getLocalFallbackResponse(string $intent): string
    {
        return match ($intent) {
            'admission' => "For **Admissions** at Prayaag School, we offer a simple conversational process. Please type **'admission'** or **'apply'** to start filling out the registration enquiry form right here, or contact our admissions office at **+91 99999 99999**.",
            'fee' => "Our **Fee Structure** for the 2026-27 session varies by grade level. Generally, it includes registration fees, tuition fees, and transportation (if applicable). For detailed class-wise fees, please visit our school front office or email us.",
            'academic' => "Prayaag School follows the **CBSE curriculum** with modern smart classes. We focus on holistic development, combining strong academics with sports, labs, music, and arts.",
            'contact' => "You can reach **Prayaag School** at:\n- **Phone**: +91 99999 99999\n- **Email**: info@prayaagschool.com\n- **Hours**: Monday to Saturday, 8:00 AM - 2:00 PM.",
            'facility' => "We provide top-notch **facilities**:\n- Smart Classrooms\n- Physics, Chemistry, Biology & Computer Labs\n- Well-stocked Library\n- Safe school bus transport across Panipat",
            'complaint' => "We take feedback seriously. Please share your concern here or email us at **grievance@prayaagschool.com**. An administrator will get back to you within 24 hours.",
            default => "Hello! Welcome to **Prayaag School**. How can I help you today? You can ask about Admissions, Fees, Academics, Facilities, or how to contact us."
        };
    }

    private function callGemini(string $apiKey, string $model, array $history, string $systemPrompt, float $temp, int $maxTokens): string
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
        $contents = [];
        foreach ($history as $msg) {
            $role = $msg['role'] === 'model' ? 'model' : 'user';
            $contents[] = ['role' => $role, 'parts' => [['text' => $msg['content']]]];
        }
        $payload = [
            'contents' => $contents,
            'generationConfig' => ['temperature' => $temp, 'maxOutputTokens' => $maxTokens],
        ];
        if (!empty($systemPrompt)) {
            $payload['systemInstruction'] = ['parts' => [['text' => $systemPrompt]]];
        }
        $response = Http::withHeaders(['Content-Type' => 'application/json'])->post($url, $payload);
        if ($response->successful()) {
            $data = $response->json();
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
        }
        Log::error('Gemini API failed: ' . $response->body());
        throw new \Exception('Gemini API call failed: ' . $response->status());
    }

    private function callOpenAI(string $apiKey, string $model, array $history, string $systemPrompt, float $temp, int $maxTokens, string $baseUrl = 'https://api.openai.com/v1/chat/completions'): string
    {
        $messages = [];
        if (!empty($systemPrompt)) $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        foreach ($history as $msg) {
            $role = $msg['role'] === 'model' ? 'assistant' : 'user';
            $messages[] = ['role' => $role, 'content' => $msg['content']];
        }
        $response = Http::withToken($apiKey)->post($baseUrl, [
            'model' => $model,
            'messages' => $messages,
            'temperature' => $temp,
            'max_tokens' => $maxTokens,
        ]);
        if ($response->successful()) {
            $data = $response->json();
            return $data['choices'][0]['message']['content'] ?? '';
        }
        Log::error('OpenAI-compatible API failed at ' . $baseUrl . ': ' . $response->body());
        throw new \Exception('OpenAI-compatible API call failed: ' . $response->status());
    }

    private function callClaude(string $apiKey, string $model, array $history, string $systemPrompt, float $temp, int $maxTokens): string
    {
        $url = "https://api.anthropic.com/v1/messages";
        $messages = [];
        foreach ($history as $msg) {
            $role = $msg['role'] === 'model' ? 'assistant' : 'user';
            $messages[] = ['role' => $role, 'content' => $msg['content']];
        }
        $headers = ['x-api-key' => $apiKey, 'anthropic-version' => '2023-06-01', 'content-type' => 'application/json'];
        $payload = ['model' => $model, 'messages' => $messages, 'max_tokens' => $maxTokens, 'temperature' => $temp];
        if (!empty($systemPrompt)) $payload['system'] = $systemPrompt;
        $response = Http::withHeaders($headers)->post($url, $payload);
        if ($response->successful()) {
            $data = $response->json();
            return $data['content'][0]['text'] ?? '';
        }
        Log::error('Claude API failed: ' . $response->body());
        throw new \Exception('Claude API call failed: ' . $response->status());
    }

    private function callHuggingFace(string $apiKey, string $model, array $history, string $systemPrompt, float $temp, int $maxTokens): string
    {
        $url = "https://api-inference.huggingface.co/models/{$model}";
        $messages = [];
        if (!empty($systemPrompt)) $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        foreach ($history as $msg) {
            $messages[] = ['role' => $msg['role'] === 'model' ? 'assistant' : 'user', 'content' => $msg['content']];
        }
        $payload = [
            'inputs' => $this->formatHuggingFaceMessages($messages),
            'parameters' => ['temperature' => $temp, 'max_new_tokens' => $maxTokens, 'return_full_text' => false],
            'options' => ['wait_for_model' => true, 'use_cache' => false],
        ];
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ])->post($url, $payload);
        if ($response->successful()) {
            $data = $response->json();
            if (isset($data[0]['generated_text'])) return $data[0]['generated_text'];
            if (isset($data['generated_text'])) return $data['generated_text'];
            return '';
        }
        Log::error('HuggingFace API failed: ' . $response->body());
        throw new \Exception('HuggingFace API call failed: ' . $response->status());
    }

    private function formatHuggingFaceMessages(array $messages): string
    {
        $formatted = '';
        foreach ($messages as $msg) {
            $role = $msg['role'] === 'system' ? '' : ($msg['role'] === 'assistant' ? 'Assistant: ' : 'User: ');
            $formatted .= "{$role}{$msg['content']}\n";
        }
        return $formatted . 'Assistant: ';
    }

    private function callOllama(string $model, array $history, string $systemPrompt, float $temp, int $maxTokens): string
    {
        $url = "http://localhost:11434/api/chat";
        $messages = [];
        if (!empty($systemPrompt)) $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        foreach ($history as $msg) {
            $role = $msg['role'] === 'model' ? 'assistant' : 'user';
            $messages[] = ['role' => $role, 'content' => $msg['content']];
        }
        $response = Http::post($url, [
            'model' => $model, 'messages' => $messages,
            'options' => ['temperature' => $temp, 'num_predict' => $maxTokens],
            'stream' => false,
        ]);
        if ($response->successful()) {
            $data = $response->json();
            return $data['message']['content'] ?? '';
        }
        Log::error('Ollama API failed: ' . $response->body());
        throw new \Exception('Ollama API call failed: ' . $response->status());
    }
}
