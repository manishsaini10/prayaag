<?php

return [
    'enabled' => env('CHATBOT_ENABLED', true),

    'ai' => [
        'default_provider' => env('CHATBOT_AI_PROVIDER', 'gemini'),
        'default_model' => env('CHATBOT_AI_MODEL', 'gemini-2.0-flash'),
        'temperature' => env('CHATBOT_AI_TEMPERATURE', 0.7),
        'max_tokens' => env('CHATBOT_AI_MAX_TOKENS', 1000),
        'providers' => [
            'gemini' => [
                'key' => env('GEMINI_API_KEY'),
                'url' => 'https://generativelanguage.googleapis.com/v1beta/models',
            ],
            'openai' => [
                'key' => env('OPENAI_API_KEY'),
                'url' => 'https://api.openai.com/v1',
            ],
            'claude' => [
                'key' => env('ANTHROPIC_API_KEY'),
                'url' => 'https://api.anthropic.com/v1',
            ],
            'openrouter' => [
                'key' => env('OPENROUTER_API_KEY'),
                'url' => 'https://openrouter.ai/api/v1',
            ],
            'ollama' => [
                'url' => env('OLLAMA_URL', 'http://localhost:11434'),
            ],
            'groq' => [
                'key' => env('GROQ_API_KEY'),
                'url' => 'https://api.groq.com/openai/v1',
            ],
            'huggingface' => [
                'key' => env('HUGGINGFACE_API_KEY'),
                'url' => 'https://api-inference.huggingface.co/models',
            ],
            'mistral' => [
                'key' => env('MISTRAL_API_KEY'),
                'url' => 'https://api.mistral.ai/v1',
            ],
            'together' => [
                'key' => env('TOGETHER_API_KEY'),
                'url' => 'https://api.together.xyz/v1',
            ],
            'deepseek' => [
                'key' => env('DEEPSEEK_API_KEY'),
                'url' => 'https://api.deepseek.com/v1',
            ],
        ],
    ],

    'realtime' => [
        'driver' => env('CHATBOT_REALTIME_DRIVER', 'reverb'),
        'typing_timeout' => 3000,
    ],

    'widget' => [
        'default_position' => 'bottom-right',
        'default_color' => '#6366f1',
        'polling_interval' => 3000,
    ],

    'tracking' => [
        'enabled' => true,
        'capture_pages' => true,
        'capture_events' => true,
        'session_timeout' => 1800,
    ],

    'automation' => [
        'max_executions_per_trigger' => 100,
        'cooldown_seconds' => 60,
    ],

    'ticket' => [
        'auto_ticket_on_offline' => true,
        'default_sla_hours' => 24,
        'prefix' => 'TKT-',
    ],

    'crm' => [
        'default_pipeline' => 'Admission Pipeline',
        'lead_sources' => ['chatbot', 'website', 'referral', 'social', 'email', 'phone', 'walk-in'],
    ],
];
