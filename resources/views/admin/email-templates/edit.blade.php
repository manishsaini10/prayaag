@extends('admin.layout')

@section('title', 'Template Studio: ' . $template->template_key)
@section('subtitle', 'Module: ' . ucfirst(str_replace('_', ' ', $template->module)))

@section('content')
<div x-data="{
    body: `{{ $template->body_html }}`,
    activeTab: 'editor',
    copiedPh: '',
    insertPlaceholder(ph) {
        const phCode = '{{' + ph + '}}';
        navigator.clipboard.writeText(phCode);
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

    <!-- TOP CONTROL BAR -->
    <div class="bg-slate-900/90 backdrop-blur-xl rounded-3xl p-6 border border-slate-700/80 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.email-templates.index') }}" class="p-2.5 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors border border-slate-700">
                ← Back
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-mono text-base font-extrabold text-white">{{ $template->template_key }}</span>
                    <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full uppercase tracking-wider border {{ $template->is_active ? 'bg-emerald-950/80 text-emerald-300 border-emerald-700' : 'bg-slate-800 text-slate-400 border-slate-700' }}">
                        {{ $template->is_active ? 'Active' : 'Disabled' }}
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">Editing template for module: <strong class="text-indigo-300 capitalize">{{ str_replace('_', ' ', $template->module) }}</strong></p>
            </div>
        </div>

        <!-- Mode Toggle Tabs (Editor / Full Preview) -->
        <div class="flex items-center gap-2">
            <div class="p-1 rounded-2xl bg-slate-800 border border-slate-700 flex items-center gap-1">
                <button @click="activeTab = 'editor'" :class="activeTab === 'editor' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-400 hover:text-white'" class="px-4 py-1.5 rounded-xl text-xs font-bold transition-all">
                    Split Studio
                </button>
                <button @click="activeTab = 'preview'" :class="activeTab === 'preview' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-400 hover:text-white'" class="px-4 py-1.5 rounded-xl text-xs font-bold transition-all">
                    Full Preview
                </button>
            </div>

            <form action="{{ route('admin.email-templates.test-send', $template->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 transition-all flex items-center gap-1.5">
                    🚀 Test Send
                </button>
            </form>
        </div>
    </div>

    <!-- MAIN STUDIO WORKSPACE -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- LEFT/CENTER: EDITOR & PREVIEW -->
        <div :class="activeTab === 'preview' ? 'lg:col-span-3' : 'lg:col-span-2'" class="space-y-6">
            <form action="{{ route('admin.email-templates.update', $template->id) }}" method="POST" class="bg-slate-900/90 backdrop-blur-xl rounded-3xl p-6 border border-slate-700/80 shadow-xl space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-indigo-300 mb-2">Email Subject Line</label>
                    <input type="text" name="subject" value="{{ old('subject', $template->subject) }}" required
                           class="w-full rounded-2xl border-slate-700 bg-slate-800 text-white font-semibold p-4 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                </div>

                <div x-show="activeTab === 'editor'" class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-indigo-300">HTML Code Editor</label>
                        <span class="text-[11px] text-slate-400 font-mono">HTML & CSS allowed</span>
                    </div>
                    <textarea name="body_html" x-model="body" rows="16" required
                              class="w-full rounded-2xl border-slate-700 bg-slate-950 text-emerald-300 p-4 text-xs font-mono leading-relaxed focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-inner transition-all"></textarea>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-800">
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-white font-bold">
                        <input type="checkbox" name="is_active" value="1" {{ $template->is_active ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span>Active Template</span>
                    </label>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.email-templates.index') }}" class="btn">Cancel</a>
                        <button type="submit" class="btn primary px-6 shadow-lg shadow-indigo-600/30">Save Changes</button>
                    </div>
                </div>
            </form>

            <!-- LIVE HTML PREVIEW FRAME -->
            <div class="bg-slate-900/90 backdrop-blur-xl rounded-3xl p-6 border border-slate-700/80 shadow-xl space-y-3">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-white flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Live Render Preview
                    </h4>
                    <span class="text-[11px] text-slate-400">Updates dynamically as you edit</span>
                </div>
                <div class="rounded-2xl border border-slate-700 overflow-hidden bg-white p-4 shadow-inner">
                    <iframe :srcdoc="body" class="w-full min-h-[350px] border-0"></iframe>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDEBAR: PLACEHOLDERS & REVISIONS -->
        <div x-show="activeTab === 'editor'" class="space-y-6">

            <!-- Placeholder Picker Panel -->
            <div class="bg-slate-900/90 backdrop-blur-xl rounded-3xl p-6 border border-slate-700/80 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-white">Template Placeholders</h4>
                    <span class="text-[10px] font-bold text-indigo-400">Click to Copy</span>
                </div>
                <p class="text-xs text-slate-300">Click any placeholder to copy its code, then paste into subject or body HTML:</p>

                <div class="flex flex-wrap gap-2 pt-1">
                    @foreach($template->available_placeholders ?? [] as $ph)
                        <button type="button" @click="insertPlaceholder('{{ $ph }}')"
                                class="w-full px-3 py-2 rounded-xl bg-slate-800 hover:bg-indigo-600/30 text-indigo-300 hover:text-white border border-slate-700 hover:border-indigo-500/50 font-mono text-xs transition-all flex items-center justify-between group">
                            <span>&#123;&#123;{{ $ph }}&#125;&#125;</span>
                            <span class="text-[10px] font-bold text-slate-400 group-hover:text-indigo-200">Copy 📋</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Revision History Panel -->
            <div class="bg-slate-900/90 backdrop-blur-xl rounded-3xl p-6 border border-slate-700/80 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-white">Revision Timeline</h4>
                    <span class="text-[11px] text-slate-400">{{ $template->revisions->count() }} Saved</span>
                </div>

                <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
                    @forelse($template->revisions as $rev)
                        <div class="p-3.5 rounded-2xl bg-slate-800/80 border border-slate-700 text-xs space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-white">{{ $rev->created_at->format('M j, Y H:i') }}</span>
                                <form action="{{ route('admin.email-templates.revert', [$template->id, $rev->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-slate-700 hover:bg-slate-600 text-[10px] font-extrabold text-white border border-slate-600 transition-colors">
                                        Restore
                                    </button>
                                </form>
                            </div>
                            <p class="text-[11px] text-slate-300 truncate">{{ $rev->subject }}</p>
                        </div>
                    @empty
                        <div class="p-4 rounded-xl bg-slate-800/50 border border-slate-700 text-center text-xs text-slate-400">
                            No previous revisions recorded yet. Revisions are created automatically whenever you save edits.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
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
