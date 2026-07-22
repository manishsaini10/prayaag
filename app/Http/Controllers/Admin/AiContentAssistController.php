<?php

namespace App\Http\Controllers\Admin;

use App\Core\Chatbot\Services\MultiLLMRouter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

class AiContentAssistController extends Controller
{
    protected MultiLLMRouter $router;

    public function __construct(MultiLLMRouter $router)
    {
        $this->router = $router;
    }

    /**
     * POST /admin/ai-assist
     * Generate content drafts, proofread grammar, rewrite tone, or create SEO meta-data.
     */
    public function generate(Request $request)
    {
        $adminId = $request->user()->id;

        // Apply rate limit of 50 AI assistance calls per day per admin user
        if (RateLimiter::tooManyAttempts('ai-content-assist:' . $adminId, 50)) {
            return response()->json([
                'success' => false,
                'message' => 'Daily AI generation limit reached (50 requests/day). Please try again tomorrow.'
            ], 429);
        }

        RateLimiter::hit('ai-content-assist:' . $adminId, 86400); // Record request hit for 24 hours

        $request->validate([
            'action'  => 'required|in:draft,grammar,rewrite,summarize,seo_meta',
            'content' => 'required_unless:action,draft|string|max:10000',
            'topic'   => 'required_if:action,draft|string|max:500',
            'tone'    => 'nullable|string|in:formal,friendly,concise,professional,exciting',
        ]);

        $action  = $request->action;
        $content = $request->content;
        $topic   = $request->topic;
        $tone    = $request->tone ?? 'formal';

        $prompt = match ($action) {
            'draft'     => $this->draftPrompt($topic),
            'grammar'   => $this->grammarPrompt($content),
            'rewrite'   => $this->rewritePrompt($content, $tone),
            'summarize' => $this->summarizePrompt($content),
            'seo_meta'  => $this->seoMetaPrompt($content),
        };

        // Retrieve AI model configuration - use a high quality model for user-facing content
        $modelConfig = $this->router->getModelConfig('admission'); // Gemimi/OpenAI fallback
        $provider    = 'gemini';
        $model       = 'gemini-1.5-pro'; // quality tier for publishing content
        
        $settings = \App\Models\Chatbot\ChatbotSetting::first();
        $aiConfig = $settings?->settings_data['ai'] ?? [];
        $apiKey   = $aiConfig["{$provider}_key"] ?? $aiConfig['api_key'] ?? '';

        if (empty($apiKey)) {
            // Fallback to whichever key is configured
            foreach (['gemini', 'openai', 'groq', 'claude'] as $p) {
                $key = $aiConfig["{$p}_key"] ?? null;
                if (!empty($key)) {
                    $provider = $p;
                    $apiKey = $key;
                    break;
                }
            }
        }

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'AI is currently offline. Please configure your Gemini API Key in the Chatbot settings page.'
            ], 500);
        }

        try {
            $result = $this->callModel($provider, $apiKey, $model, $prompt);
            return response()->json([
                'success' => true,
                'result'  => $result
            ]);
        } catch (\Exception $e) {
            Log::error('AI Content Assistant error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'AI generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unified client call router.
     */
    protected function callModel(string $provider, string $apiKey, string $model, string $prompt): string
    {
        if ($provider === 'gemini') {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
            $payload = [
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 2000]
            ];
            $response = Http::withHeaders(['Content-Type' => 'application/json'])->post($url, $payload);
            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            }
            throw new \Exception('Gemini API call failed: ' . $response->body());
        }

        // Generic OpenAI fallback
        $url = 'https://api.openai.com/v1/chat/completions';
        if ($provider === 'groq') {
            $url = 'https://api.groq.com/openai/v1/chat/completions';
            $model = 'llama-3.1-70b-versatile';
        }

        $response = Http::withToken($apiKey)->post($url, [
            'model'       => $model,
            'messages'    => [['role' => 'user', 'content' => $prompt]],
            'temperature' => 0.7,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['choices'][0]['message']['content'] ?? '';
        }
        throw new \Exception("{$provider} API call failed: " . $response->body());
    }

    protected function draftPrompt(string $topic): string
    {
        return "Write a well-structured draft blog post or notice for a school website about: \"{$topic}\". "
             . "Tone: warm, informative, suitable for school website readers (parents, students, staff). "
             . "Keep it formatted nicely with headings. Do not include page layout styling or raw HTML headers, just write clean, friendly text with Markdown headings.";
    }

    protected function grammarPrompt(string $content): string
    {
        return "Proofread and correct grammar, spelling, typos, and clarity issues in the following text. "
             . "Return ONLY the corrected text, do not add any explanations or notes:\n\n{$content}";
    }

    protected function rewritePrompt(string $content, string $tone): string
    {
        return "Rewrite the following text in a {$tone} tone, keeping the same meaning and approximately same length. "
             . "Return ONLY the rewritten text, do not add explanations:\n\n{$content}";
    }

    protected function summarizePrompt(string $content): string
    {
        return "Summarize the following text in 2-3 sentences, suitable as a short summary or excerpt:\n\n{$content}";
    }

    protected function seoMetaPrompt(string $content): string
    {
        return "Based on the content below, generate an SEO meta title (under 60 characters) and a meta description (under 160 characters) that are highly engaging and keyword-rich. "
             . "Respond ONLY with a valid JSON object in this exact format (no markdown blocks, no other text): "
             . "{\"title\": \"SEO Title here\", \"description\": \"SEO Description here\"}\n\nContent:\n{$content}";
    }
}
