<?php

namespace App\Core\Chatbot\Services;

class MultiLLMRouter
{
    private const INTENTS = [
        'admission' => ['admission', 'enroll', 'enrollment', 'register', 'registration', 'apply', 'application', 'join', 'admit', 'seat', 'form', 'prospectus', 'brochure'],
        'fee' => ['fee', 'fees', 'payment', 'pay', 'cost', 'price', 'tuition', 'scholarship', 'concession', 'installment', 'refund', 'due', 'amount'],
        'academic' => ['academic', 'curriculum', 'subject', 'syllabus', 'exam', 'examination', 'result', 'grade', 'marks', 'class', 'timetable', 'schedule', 'holiday', 'vacation'],
        'contact' => ['contact', 'phone', 'call', 'email', 'address', 'location', 'reach', 'office', 'timing', 'hours', 'visit', 'meeting', 'appointment'],
        'facility' => ['facility', 'lab', 'library', 'sport', 'playground', 'transport', 'bus', 'cafeteria', 'hostel', 'infrastructure', 'smart class', 'computer'],
        'complaint' => ['complaint', 'issue', 'problem', 'grievance', 'feedback', 'suggest', 'unsatisfied', 'poor', 'bad', 'dissatisfied'],
    ];

    private const INTENT_PROMPTS = [
        'admission' => "You are a school admissions counselor for Prayaag School. Be helpful, warm, and provide clear information about the admission process, required documents, age criteria, and deadlines. Encourage the visitor to fill out the admission inquiry form.",
        'fee' => "You are a school fee management assistant for Prayaag School. Provide clear information about fee structure, payment options, scholarships, and concessions. Be transparent about all costs.",
        'academic' => "You are a school academic coordinator for Prayaag School. Provide information about curriculum, subjects, exams, academic calendar, and co-curricular activities. Be informative and encouraging.",
        'contact' => "You are a school front-desk assistant for Prayaag School. Provide contact details, address, office hours, and directions. Be polite and helpful in directing visitors to the right channel.",
        'facility' => "You are a school infrastructure guide for Prayaag School. Describe facilities, labs, library, sports amenities, transport, and other infrastructure. Be proud and detailed.",
        'complaint' => "You are a school grievance handling assistant for Prayaag School. Listen empathetically, apologize for the inconvenience, and assure the visitor that their concern has been noted. Provide the complaint email or suggest contacting the school office for urgent matters.",
    ];

    private const INTENT_MODELS = [
        'admission' => ['provider' => 'gemini', 'model' => 'gemini-1.5-flash', 'temperature' => 0.6],
        'fee' => ['provider' => 'gemini', 'model' => 'gemini-1.5-flash', 'temperature' => 0.5],
        'academic' => ['provider' => 'gemini', 'model' => 'gemini-1.5-flash', 'temperature' => 0.7],
        'contact' => ['provider' => 'gemini', 'model' => 'gemini-2.0-flash', 'temperature' => 0.4],
        'facility' => ['provider' => 'gemini', 'model' => 'gemini-1.5-flash', 'temperature' => 0.7],
        'complaint' => ['provider' => 'gemini', 'model' => 'gemini-2.0-flash', 'temperature' => 0.5],
        'general' => ['provider' => 'gemini', 'model' => 'gemini-1.5-flash', 'temperature' => 0.7],
    ];

    public const PROVIDER_MODELS = [
        'gemini'      => ['gemini-1.5-flash', 'gemini-2.0-flash', 'gemini-1.5-pro'],
        'openai'      => ['gpt-4o-mini', 'gpt-4o', 'gpt-4-turbo', 'gpt-3.5-turbo'],
        'claude'      => ['claude-3-haiku-20240307', 'claude-3-sonnet-20240229', 'claude-3-opus-20240229'],
        'openrouter'  => ['openai/gpt-4o-mini', 'meta-llama/llama-3.1-8b-instruct', 'mistralai/mistral-7b-instruct'],
        'ollama'      => ['llama3.2', 'llama3.1', 'mistral', 'phi3', 'gemma2'],
        'groq'        => ['mixtral-8x7b-32768', 'llama-3.1-70b-versatile', 'llama-3.1-8b-instant', 'gemma2-9b-it', 'llama-3.2-90b-vision-preview'],
        'huggingface' => ['HuggingFaceH4/zephyr-7b-beta', 'microsoft/phi-3-mini-4k-instruct', 'mistralai/Mistral-7B-Instruct-v0.3', 'google/gemma-2-2b-it'],
        'mistral'     => ['mistral-small-latest', 'open-mistral-7b', 'mistral-medium-latest'],
        'together'    => ['mistralai/Mixtral-8x7B-Instruct-v0.1', 'meta-llama/Llama-3-8b-chat-hf', 'google/gemma-2-27b-it'],
        'deepseek'    => ['deepseek-chat', 'deepseek-reasoner'],
    ];

    public function detectIntent(string $input): string
    {
        $lower = strtolower($input);
        $scores = [];

        foreach (self::INTENTS as $intent => $keywords) {
            $score = 0;
            foreach ($keywords as $keyword) {
                if (str_contains($lower, $keyword)) {
                    $score++;
                }
            }
            if ($score > 0) {
                $scores[$intent] = $score;
            }
        }

        if (empty($scores)) {
            return 'general';
        }

        arsort($scores);
        return array_key_first($scores);
    }

    public function getSystemPrompt(string $intent, string $kbContext = ''): string
    {
        $base = self::INTENT_PROMPTS[$intent] ?? self::INTENT_PROMPTS['admission'];
        return $base . "\n\nUse the following school information to answer accurately:\n" . ($kbContext ?: 'No specific context available. Answer based on general school knowledge.');
    }

    public function getModelConfig(string $intent): array
    {
        return self::INTENT_MODELS[$intent] ?? self::INTENT_MODELS['general'];
    }
}
