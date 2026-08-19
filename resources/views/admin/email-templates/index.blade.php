@extends('admin.layout')

@section('title', 'Email Template Studio')
@section('subtitle', 'Manage dynamic email templates, placeholders, and automated notifications across all modules')

@section('content')
<div x-data="{
    search: '',
    selectedModule: 'all',
    copiedPh: '',
    copyPlaceholder(ph) {
        navigator.clipboard.writeText('{{' + ph + '}}');
        this.copiedPh = ph;
        setTimeout(() => this.copiedPh = '', 2000);
    }
}" class="space-y-6">

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm font-semibold flex items-center justify-between shadow-lg backdrop-blur-md">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-emerald-500/20 grid place-items-center text-emerald-400 font-bold text-base">✓</span>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-400 hover:text-white text-xs uppercase font-bold">Dismiss</button>
        </div>
    @endif

    <!-- HERO HEADER CARD -->
    <div class="relative overflow-hidden rounded-3xl p-8 border border-white/10 shadow-2xl"
         style="background: linear-gradient(135deg, rgba(79,70,229,0.9) 0%, rgba(124,58,237,0.85) 50%, rgba(219,39,119,0.8) 100%);">
        <!-- Glow Overlay -->
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-pink-500/30 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-indigo-500/30 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md border border-white/20 text-xs font-bold text-white tracking-wide uppercase">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Dynamic Engine Active
                </div>
                <h2 class="text-3xl font-extrabold text-white tracking-tight">Email Template Studio</h2>
                <p class="text-indigo-100 text-sm max-w-xl leading-relaxed">
                    Customise transactional emails, auto-replies, and newsletter broadcasts. Real-time placeholder rendering with zero code required.
                </p>
            </div>

            <!-- Stats Badge Group -->
            <div class="grid grid-cols-3 gap-3">
                <div class="px-4 py-3 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 text-center">
                    <span class="block text-2xl font-black text-white">{{ $templatesGrouped->flatten()->count() }}</span>
                    <span class="text-[11px] font-semibold text-indigo-100 uppercase tracking-wider">Templates</span>
                </div>
                <div class="px-4 py-3 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 text-center">
                    <span class="block text-2xl font-black text-emerald-300">{{ $templatesGrouped->flatten()->where('is_active', true)->count() }}</span>
                    <span class="text-[11px] font-semibold text-indigo-100 uppercase tracking-wider">Active</span>
                </div>
                <div class="px-4 py-3 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 text-center">
                    <span class="block text-2xl font-black text-pink-200">{{ $templatesGrouped->keys()->count() }}</span>
                    <span class="text-[11px] font-semibold text-indigo-100 uppercase tracking-wider">Modules</span>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="bg-slate-900/90 backdrop-blur-xl rounded-2xl p-4 border border-slate-700/80 shadow-lg flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Module Filter Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto pb-2 md:pb-0 scrollbar-none">
            <button @click="selectedModule = 'all'"
                    :class="selectedModule === 'all' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'bg-slate-800/80 text-slate-300 hover:bg-slate-700 hover:text-white'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex-shrink-0">
                All Modules ({{ $templatesGrouped->flatten()->count() }})
            </button>
            @foreach($templatesGrouped as $mod => $items)
                <button @click="selectedModule = '{{ $mod }}'"
                        :class="selectedModule === '{{ $mod }}' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'bg-slate-800/80 text-slate-300 hover:bg-slate-700 hover:text-white'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition-all capitalize flex-shrink-0">
                    {{ str_replace('_', ' ', $mod) }} ({{ $items->count() }})
                </button>
            @endforeach
        </div>

        <!-- Search Box -->
        <div class="relative w-full md:w-72">
            <input type="text" x-model="search" placeholder="Search key or subject..."
                   class="w-full rounded-xl border-slate-700 bg-slate-800/90 text-white placeholder-slate-400 pl-10 pr-4 py-2 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
            <span class="absolute left-3 top-2.5 text-slate-400">🔍</span>
        </div>
    </div>

    <!-- TEMPLATES LIST BY MODULE -->
    <div class="space-y-6">
        @foreach($templatesGrouped as $module => $templates)
            <div x-show="selectedModule === 'all' || selectedModule === '{{ $module }}'"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="bg-slate-900/90 backdrop-blur-xl rounded-3xl p-6 border border-slate-700/80 shadow-xl space-y-4">

                <!-- Module Header -->
                @php
                    $moduleColors = [
                        'careers' => 'from-blue-500 to-cyan-500',
                        'enquiry' => 'from-emerald-500 to-teal-500',
                        'newsletter' => 'from-pink-500 to-rose-500',
                        'video_testimonials' => 'from-purple-500 to-indigo-500',
                        'mess_menu' => 'from-amber-500 to-orange-500',
                        'chatbot' => 'from-violet-500 to-fuchsia-500',
                    ];
                    $gradient = $moduleColors[$module] ?? 'from-indigo-500 to-purple-500';
                @endphp

                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr {{ $gradient }} grid place-items-center text-white font-bold shadow-md">
                            {{ strtoupper(substr($module, 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-extrabold text-white capitalize tracking-tight">
                                {{ str_replace('_', ' ', $module) }} Module
                            </h3>
                            <p class="text-xs text-slate-400">Automated triggers and notification templates</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-slate-800 border border-slate-700 text-xs font-mono font-bold text-slate-300">
                        {{ $templates->count() }} Templates
                    </span>
                </div>

                <!-- Template Cards -->
                <div class="grid grid-cols-1 gap-4">
                    @foreach($templates as $t)
                        <div x-show="search === '' || '{{ strtolower($t->template_key) }}'.includes(search.toLowerCase()) || '{{ strtolower($t->subject) }}'.includes(search.toLowerCase())"
                             class="group p-5 rounded-2xl bg-slate-800/60 hover:bg-slate-800/90 border border-slate-700/60 hover:border-indigo-500/50 transition-all duration-200 flex flex-col lg:flex-row lg:items-center justify-between gap-5">

                            <div class="space-y-2.5 flex-1 min-w-0">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <span class="font-mono text-sm font-extrabold text-white group-hover:text-indigo-300 transition-colors">
                                        {{ $t->template_key }}
                                    </span>

                                    <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full uppercase tracking-wider border {{ $t->is_active ? 'bg-emerald-950/80 text-emerald-300 border-emerald-700/80' : 'bg-slate-900 text-slate-400 border-slate-700' }}">
                                        {{ $t->is_active ? '● Active' : '○ Disabled' }}
                                    </span>
                                </div>

                                <p class="text-xs text-slate-300 font-medium truncate">
                                    <span class="text-slate-400 font-normal">Subject:</span> {{ $t->subject }}
                                </p>

                                <!-- Placeholders Chips -->
                                @if(!empty($t->available_placeholders))
                                    <div class="flex items-center gap-1.5 flex-wrap pt-1">
                                        <span class="text-[11px] font-semibold text-slate-400">Placeholders:</span>
                                        @foreach($t->available_placeholders as $ph)
                                            <button type="button" @click="copyPlaceholder('{{ $ph }}')"
                                                    title="Click to copy {{ $ph }}"
                                                    class="px-2 py-0.5 rounded-lg bg-slate-900/90 hover:bg-indigo-600/30 text-indigo-300 hover:text-white border border-indigo-500/30 font-mono text-[10px] transition-all hover:scale-105 active:scale-95 flex items-center gap-1">
                                                <span>&#123;&#123;{{ $ph }}&#125;&#125;</span>
                                                <span x-show="copiedPh === '{{ $ph }}'" class="text-emerald-400 font-bold">✓</span>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- Actions Group -->
                            <div class="flex items-center gap-2 flex-shrink-0 self-start lg:self-center pt-2 lg:pt-0 border-t lg:border-t-0 border-slate-700/50">
                                <form action="{{ route('admin.email-templates.test-send', $t->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-900 hover:bg-slate-700 text-slate-200 border border-slate-700 transition-all flex items-center gap-1.5 shadow-sm">
                                        <span>🚀</span> Test Send
                                    </button>
                                </form>

                                <form action="{{ route('admin.email-templates.toggle', $t->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-bold border transition-all {{ $t->is_active ? 'bg-amber-950/60 text-amber-300 border-amber-800/80 hover:bg-amber-900' : 'bg-emerald-950/60 text-emerald-300 border-emerald-800/80 hover:bg-emerald-900' }}">
                                        {{ $t->is_active ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>

                                <a href="{{ route('admin.email-templates.edit', $t->id) }}" class="px-4 py-2 rounded-xl text-xs font-extrabold bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white shadow-lg shadow-indigo-600/30 transition-all transform hover:-translate-y-0.5 flex items-center gap-1.5">
                                    <span>✏️</span> Edit
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <!-- Toast Notification -->
    <div x-show="copiedPh !== ''"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-6 right-6 z-50 px-5 py-3 rounded-2xl bg-indigo-600 text-white font-bold text-xs shadow-2xl border border-indigo-400/40 flex items-center gap-2" x-cloak>
        <span>📋</span>
        <span>Copied <code x-text="'{{' + copiedPh + '}}'" class="font-mono bg-white/20 px-1.5 py-0.5 rounded text-white"></code> to clipboard!</span>
    </div>
</div>
@endsection
