@extends('admin.layout')

@section('title', 'AI Chatbot Settings')

@section('actions')
    <a href="{{ url('/admin/chatbot/conversations') }}" class="btn-primary inline-flex items-center gap-1.5">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        <span>Live Operator Panel</span>
    </a>
    <a href="{{ url('/admin/chatbot/kb') }}" class="btn-secondary inline-flex items-center gap-1.5">
        <span>Knowledge Base</span>
    </a>
@endsection

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="px-4 py-3 rounded-xl text-sm" style="background:#dcfce7;color:#166534">{{ session('success') }}</div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="stat-card"><div class="stat-value">{{ $stats['total_chats'] }}</div><div class="stat-label">Total Chats</div></div>
        <div class="stat-card"><div class="stat-value" style="color:var(--primary)">{{ $stats['open_chats'] }}</div><div class="stat-label">Open Chats</div></div>
        <div class="stat-card"><div class="stat-value" style="color:#6b7280">{{ $stats['closed_chats'] }}</div><div class="stat-label">Closed Chats</div></div>
        <div class="stat-card"><div class="stat-value" style="color:#16a34a">{{ $stats['ai_responses'] }}</div><div class="stat-label">AI Handled</div></div>
        <div class="stat-card"><div class="stat-value" style="color:#a855f7">{{ $stats['leads_count'] }}</div><div class="stat-label">Leads Captured</div></div>
    </div>

    {{-- Form --}}
    <form action="{{ url('/admin/chatbot/settings') }}" method="POST" class="card space-y-6">
        @csrf
        
        <div>
            <h3 class="text-lg font-bold" style="color:var(--text)">Chatbot Features</h3>
            <p class="text-sm mt-1" style="color:var(--text-muted)">Configure which options are active inside the chatbot widget.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="enable_chatbot" value="1" {{ $settings->enable_chatbot ? 'checked' : '' }} class="mt-1">
                <div>
                    <span class="font-semibold block" style="color:var(--text)">Enable Chatbot Widget</span>
                    <span class="text-xs" style="color:var(--text-muted)">Show floating chatbot on pages</span>
                </div>
            </label>
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="enable_ai" value="1" {{ $settings->enable_ai ? 'checked' : '' }} class="mt-1">
                <div>
                    <span class="font-semibold block" style="color:var(--text)">Enable AI Responses</span>
                    <span class="text-xs" style="color:var(--text-muted)">Let LLM auto-answer questions</span>
                </div>
            </label>
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="enable_live_agent" value="1" {{ $settings->enable_live_agent ? 'checked' : '' }} class="mt-1">
                <div>
                    <span class="font-semibold block" style="color:var(--text)">Enable Live Agent Handover</span>
                    <span class="text-xs" style="color:var(--text-muted)">Transfer chats to operators</span>
                </div>
            </label>
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="enable_offline_form" value="1" {{ $settings->enable_offline_form ? 'checked' : '' }} class="mt-1">
                <div>
                    <span class="font-semibold block" style="color:var(--text)">Offline Ticket Form</span>
                    <span class="text-xs" style="color:var(--text-muted)">Collect queries when offline</span>
                </div>
            </label>
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="enable_kb" value="1" {{ $settings->enable_kb ? 'checked' : '' }} class="mt-1">
                <div>
                    <span class="font-semibold block" style="color:var(--text)">Enable Knowledge Base</span>
                    <span class="text-xs" style="color:var(--text-muted)">Retrieve answers from school content</span>
                </div>
            </label>
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="enable_visitor_tracking" value="1" {{ $settings->enable_visitor_tracking ? 'checked' : '' }} class="mt-1">
                <div>
                    <span class="font-semibold block" style="color:var(--text)">Visitor Tracking</span>
                    <span class="text-xs" style="color:var(--text-muted)">Log UTM sources and browser details</span>
                </div>
            </label>
        </div>

        <hr style="border-top:1px solid var(--border)">

        {{-- Widget Branding & Styling --}}
        <div>
            <h3 class="text-lg font-bold" style="color:var(--text)">Appearance & Customization</h3>
            <p class="text-sm mt-1" style="color:var(--text-muted)">Design the widget to match the Prayaag School theme.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Widget Position</label>
                <select name="widget_position" class="w-full">
                    <option value="bottom-right" {{ $settings->widget_position === 'bottom-right' ? 'selected' : '' }}>Bottom Right</option>
                    <option value="bottom-left" {{ $settings->widget_position === 'bottom-left' ? 'selected' : '' }}>Bottom Left</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Widget Shape</label>
                <select name="widget_shape" class="w-full">
                    <option value="rounded" {{ $settings->widget_shape === 'rounded' ? 'selected' : '' }}>Rounded (Standard)</option>
                    <option value="square" {{ $settings->widget_shape === 'square' ? 'selected' : '' }}>Square</option>
                    <option value="bubble" {{ $settings->widget_shape === 'bubble' ? 'selected' : '' }}>Floating Bubble</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Launcher Style</label>
                <select name="launcher_style" class="w-full">
                    <option value="icon" {{ $settings->launcher_style === 'icon' ? 'selected' : '' }}>Standard Icon</option>
                    <option value="avatar" {{ $settings->launcher_style === 'avatar' ? 'selected' : '' }}>Agent Avatar</option>
                    <option value="button" {{ $settings->launcher_style === 'button' ? 'selected' : '' }}>Text Button</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Primary Color (Hex)</label>
                <input type="color" name="primary_color" value="{{ $settings->primary_color }}" class="w-full h-10 p-0 border-0 cursor-pointer">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Secondary Color (Hex)</label>
                <input type="color" name="secondary_color" value="{{ $settings->secondary_color }}" class="w-full h-10 p-0 border-0 cursor-pointer">
            </div>
        </div>

        <hr style="border-top:1px solid var(--border)">

        {{-- AI Engine Configuration --}}
        <div>
            <h3 class="text-lg font-bold" style="color:var(--text)">AI Model Configuration</h3>
            <p class="text-sm mt-1" style="color:var(--text-muted)">Configure your preferred Large Language Model (LLM) API details.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">AI Provider</label>
                <select name="settings_data[ai][provider]" class="w-full">
                    <option value="gemini" {{ ($settings->settings_data['ai']['provider'] ?? '') === 'gemini' ? 'selected' : '' }}>Google Gemini</option>
                    <option value="openai" {{ ($settings->settings_data['ai']['provider'] ?? '') === 'openai' ? 'selected' : '' }}>OpenAI (ChatGPT)</option>
                    <option value="claude" {{ ($settings->settings_data['ai']['provider'] ?? '') === 'claude' ? 'selected' : '' }}>Anthropic Claude</option>
                    <option value="openrouter" {{ ($settings->settings_data['ai']['provider'] ?? '') === 'openrouter' ? 'selected' : '' }}>OpenRouter (Universal)</option>
                    <option value="ollama" {{ ($settings->settings_data['ai']['provider'] ?? '') === 'ollama' ? 'selected' : '' }}>Ollama (Local LLM)</option>
                    <option value="groq" {{ ($settings->settings_data['ai']['provider'] ?? '') === 'groq' ? 'selected' : '' }}>Groq (Free)</option>
                    <option value="huggingface" {{ ($settings->settings_data['ai']['provider'] ?? '') === 'huggingface' ? 'selected' : '' }}>Hugging Face (Free)</option>
                    <option value="mistral" {{ ($settings->settings_data['ai']['provider'] ?? '') === 'mistral' ? 'selected' : '' }}>Mistral AI (Free)</option>
                    <option value="together" {{ ($settings->settings_data['ai']['provider'] ?? '') === 'together' ? 'selected' : '' }}>Together AI</option>
                    <option value="deepseek" {{ ($settings->settings_data['ai']['provider'] ?? '') === 'deepseek' ? 'selected' : '' }}>DeepSeek (Cheap)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Model Selection</label>
                <input type="text" name="settings_data[ai][model]" value="{{ $settings->settings_data['ai']['model'] ?? 'gemini-1.5-flash' }}" placeholder="gemini-1.5-flash" class="w-full">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Gemini API Key</label>
                <input type="password" name="settings_data[ai][gemini_key]" value="{{ $settings->settings_data['ai']['gemini_key'] ?? '' }}" placeholder="Google Gemini API Key" class="w-full">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">OpenAI API Key</label>
                <input type="password" name="settings_data[ai][openai_key]" value="{{ $settings->settings_data['ai']['openai_key'] ?? '' }}" placeholder="OpenAI API Key" class="w-full">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Groq API Key</label>
                <input type="password" name="settings_data[ai][groq_key]" value="{{ $settings->settings_data['ai']['groq_key'] ?? '' }}" placeholder="Groq API Key (free at console.groq.com)" class="w-full">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Hugging Face API Key</label>
                <input type="password" name="settings_data[ai][huggingface_key]" value="{{ $settings->settings_data['ai']['huggingface_key'] ?? '' }}" placeholder="Hugging Face Token" class="w-full">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Mistral API Key</label>
                <input type="password" name="settings_data[ai][mistral_key]" value="{{ $settings->settings_data['ai']['mistral_key'] ?? '' }}" placeholder="Mistral AI API Key" class="w-full">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Temperature (0.0 to 1.0)</label>
                <input type="number" step="0.1" min="0" max="1" name="settings_data[ai][temperature]" value="{{ $settings->settings_data['ai']['temperature'] ?? '0.7' }}" class="w-full">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Max Output Tokens</label>
                <input type="number" name="settings_data[ai][max_tokens]" value="{{ $settings->settings_data['ai']['max_tokens'] ?? '800' }}" class="w-full">
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary">Save Settings</button>
        </div>
    </form>
</div>
@endsection
