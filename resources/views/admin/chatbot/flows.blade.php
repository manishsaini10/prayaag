@extends('admin.layout')

@section('title', 'Visual Chatbot Flow Builder')

@section('actions')
    <a href="{{ url('/admin/chatbot') }}" class="btn-secondary inline-flex items-center gap-1.5">
        &larr; Back to Settings
    </a>
@endsection

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="px-4 py-3 rounded-xl text-sm" style="background:#dcfce7;color:#166534">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Left Panel: Nodes library --}}
        <div class="card space-y-4">
            <h3 class="font-bold text-base" style="color:var(--text)">Flow Builder Nodes</h3>
            <p class="text-xs" style="color:var(--text-muted)">Drag and drop nodes to create conversation trees for visitors.</p>

            <div class="space-y-2">
                <div class="p-3 border rounded-lg cursor-grab hover:bg-surface-2/50 transition flex items-center gap-2" style="border-color:var(--border)">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                    <span class="text-xs font-semibold" style="color:var(--text)">Greeting / Welcome Node</span>
                </div>
                <div class="p-3 border rounded-lg cursor-grab hover:bg-surface-2/50 transition flex items-center gap-2" style="border-color:var(--border)">
                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                    <span class="text-xs font-semibold" style="color:var(--text)">Quick Reply / Buttons</span>
                </div>
                <div class="p-3 border rounded-lg cursor-grab hover:bg-surface-2/50 transition flex items-center gap-2" style="border-color:var(--border)">
                    <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                    <span class="text-xs font-semibold" style="color:var(--text)">Collect Information Form</span>
                </div>
                <div class="p-3 border rounded-lg cursor-grab hover:bg-surface-2/50 transition flex items-center gap-2" style="border-color:var(--border)">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                    <span class="text-xs font-semibold" style="color:var(--text)">Transfer to Human Node</span>
                </div>
            </div>
        </div>

        {{-- Center Canvas: Editor simulation --}}
        <div class="lg:col-span-3 card flex flex-col justify-between" style="min-height:500px">
            <div class="flex justify-between items-center pb-4 border-b" style="border-color:var(--border)">
                <h3 class="font-bold text-lg" style="color:var(--text)">Conversation Canvas</h3>
                <span class="text-xs px-2 py-1 rounded bg-green-100 text-green-800 font-semibold">Live Mode</span>
            </div>

            {{-- Interactive simulation area --}}
            <div class="flex-grow flex items-center justify-center p-8 bg-surface-2/30 rounded-xl my-4 border border-dashed" style="border-color:var(--border)">
                <div class="text-center max-w-sm space-y-3">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-12 h-12 mx-auto text-muted" style="color:var(--text-muted)">
                        <path d="M18 3H6a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3V6a3 3 0 0 0-3-3z"/>
                        <path d="M9 17h6M9 13h6M9 9h2"/>
                    </svg>
                    <h4 class="font-bold text-sm" style="color:var(--text)">Flow Editor Visual Board</h4>
                    <p class="text-xs" style="color:var(--text-muted)">Your interactive graph layout configuration is saved directly as a JSON flow tree schema below.</p>
                </div>
            </div>

            <form action="{{ url('/admin/chatbot/flows') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color:var(--text)">Flow Configuration Name</label>
                        <input type="text" name="name" value="{{ $flow->name ?? 'Default School flow' }}" required class="w-full">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1" style="color:var(--text)">Flow JSON Schema Graph</label>
                    <textarea name="flow_data" rows="5" class="w-full text-xs font-mono bg-surface-2 p-3 rounded-lg border" style="border-color:var(--border)">{{ $flow ? json_encode($flow->flow_data, JSON_PRETTY_PRINT) : '{}' }}</textarea>
                </div>
                <button type="submit" class="btn-primary">Save Visual Flow Schema</button>
            </form>
        </div>
    </div>
</div>
@endsection
