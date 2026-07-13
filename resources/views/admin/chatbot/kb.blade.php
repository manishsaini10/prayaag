@extends('admin.layout')

@section('title', 'Chatbot Knowledge Base')

@section('actions')
    <a href="{{ url('/admin/chatbot') }}" class="btn-secondary inline-flex items-center gap-1.5 mr-2">
        &larr; Back to Settings
    </a>
    <form action="{{ url('/admin/chatbot/kb/index-cms') }}" method="POST" class="inline">
        @csrf
        <button type="submit" class="btn-primary inline-flex items-center gap-1.5">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 1 1 21.2 8H18.5"/></svg>
            <span>Index CMS Pages & Blogs</span>
        </button>
    </form>
@endsection

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="px-4 py-3 rounded-xl text-sm" style="background:#dcfce7;color:#166534">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Custom documents list --}}
        <div class="lg:col-span-2 card p-0 overflow-hidden">
            <div class="p-4 border-b" style="border-color:var(--border)">
                <h3 class="font-bold text-lg" style="color:var(--text)">Indexed Knowledge Documents</h3>
                <p class="text-xs mt-0.5" style="color:var(--text-muted)">Documents currently searchable by the AI Chatbot.</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border)">
                            <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Title</th>
                            <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Type</th>
                            <th class="text-right px-4 py-3 font-semibold" style="color:var(--text-muted)">Chunks</th>
                            <th class="text-right px-4 py-3 font-semibold" style="color:var(--text-muted)">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents as $doc)
                            <tr style="border-bottom:1px solid var(--border)" class="hover:bg-surface-2/50 transition">
                                <td class="px-4 py-3">
                                    <div class="font-medium" style="color:var(--text)">{{ $doc->title }}</div>
                                    <div class="text-xs" style="color:var(--text-muted)">ID: {{ $doc->source_id ?? 'custom' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge">{{ strtoupper($doc->type) }}</span>
                                </td>
                                <td class="px-4 py-3 text-right" style="color:var(--text-muted)">
                                    {{ $doc->chunks_count }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <form action="{{ url('/admin/chatbot/kb/' . $doc->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this document?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="color:#ef4444" class="text-xs font-semibold hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-sm" style="color:var(--text-muted)">
                                    No knowledge documents indexed yet. Click "Index CMS Pages & Blogs" to load pages.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($documents->hasPages())
                <div class="p-4 border-t" style="border-color:var(--border)">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>

        {{-- Add Custom Doc form --}}
        <div class="card space-y-4">
            <div>
                <h3 class="font-bold text-lg" style="color:var(--text)">Add Custom Knowledge</h3>
                <p class="text-xs mt-0.5" style="color:var(--text-muted)">Insert custom school guidelines, fees structure, or FAQs.</p>
            </div>

            <form action="{{ url('/admin/chatbot/kb/upload') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold mb-1" style="color:var(--text)">Document Title</label>
                    <input type="text" name="title" required placeholder="e.g. Nursery Admission Fee Details 2026" class="w-full">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1" style="color:var(--text)">Doc Type</label>
                    <select name="doc_type" class="w-full">
                        <option value="faq">FAQ</option>
                        <option value="policy">Policy / Rules</option>
                        <option value="general">General Info</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1" style="color:var(--text)">Text Content</label>
                    <textarea name="text_content" required rows="10" placeholder="Type or paste document paragraphs here..." class="w-full text-sm"></textarea>
                </div>
                <button type="submit" class="btn-primary w-full">Index Document</button>
            </form>
        </div>
    </div>
</div>
@endsection
