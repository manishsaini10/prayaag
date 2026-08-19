@extends('admin.layout')

@section('title', 'Email Providers')
@section('subtitle', 'Configure live SMTP, Hostinger, Zoho, or API email providers')

@section('actions')
    <a href="{{ route('admin.email-providers.create') }}" class="btn primary">
        <x-admin.icon name="plus"/> Add Provider
    </a>
@endsection

@section('content')
    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-emerald-950/80 text-emerald-200 border border-emerald-800 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 rounded-xl bg-rose-950/80 text-rose-200 border border-rose-800 text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($providers as $p)
            <div class="bg-slate-900/90 rounded-2xl p-6 border {{ $p->is_active ? 'border-indigo-500 ring-2 ring-indigo-500/30' : 'border-slate-700/80' }} shadow-md flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full uppercase tracking-wider {{ $p->is_active ? 'bg-indigo-900/90 text-indigo-200 border border-indigo-700' : 'bg-slate-800 text-slate-400 border border-slate-700' }}">
                            {{ $p->is_active ? 'Active Provider' : 'Inactive' }}
                        </span>
                        <span class="text-xs text-slate-400 font-mono">Priority: #{{ $p->priority_order }}</span>
                    </div>

                    <h3 class="text-lg font-bold text-white mb-1">{{ $p->label }}</h3>
                    <p class="text-xs text-indigo-300 font-mono uppercase tracking-wide mb-4">{{ $p->provider_key }}</p>

                    <div class="space-y-2 mb-6">
                        <div class="flex items-center justify-between text-xs text-slate-300">
                            <span>Verification Status:</span>
                            @if($p->is_verified)
                                <span class="text-emerald-400 font-semibold flex items-center gap-1">✓ Verified</span>
                            @else
                                <span class="text-amber-400 font-semibold">Not Tested / Failed</span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between text-xs text-slate-300">
                            <span>Last Tested:</span>
                            <span class="text-slate-400">{{ $p->last_tested_at ? $p->last_tested_at->diffForHumans() : 'Never' }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-800 flex items-center justify-between gap-2" x-data="{ testing: false }">
                    <div class="flex items-center gap-2">
                        @if(!$p->is_active)
                            <form action="{{ route('admin.email-providers.set-active', $p->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-950/80 text-indigo-200 hover:bg-indigo-900 border border-indigo-800 transition-colors">
                                    Set Active
                                </button>
                            </form>
                        @endif

                        <button @click="testing = true; fetch('{{ route('admin.email-providers.test', $p->id) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } }).then(r => r.json()).then(d => { testing = false; alert(d.message); window.location.reload(); })" :disabled="testing" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 transition-colors">
                            <span x-show="!testing">Test</span>
                            <span x-show="testing" x-cloak>Testing...</span>
                        </button>
                    </div>

                    <div class="flex items-center gap-1">
                        <a href="{{ route('admin.email-providers.edit', $p->id) }}" class="px-2.5 py-1 text-xs text-slate-300 hover:text-white rounded-lg hover:bg-slate-800 transition-colors">
                            Edit
                        </a>
                        @if(!$p->is_active)
                            <form action="{{ route('admin.email-providers.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Delete this provider?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1 text-xs text-rose-400 hover:text-rose-300 rounded-lg hover:bg-slate-800 transition-colors">
                                    Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 text-center bg-slate-900/90 rounded-2xl border border-slate-700/80 shadow-md">
                <p class="text-slate-300 mb-4">No custom email providers configured. The system is currently using the Log Provider (development mode).</p>
                <a href="{{ route('admin.email-providers.create') }}" class="btn primary inline-flex items-center gap-2">
                    <x-admin.icon name="plus"/> Add Hostinger / Zoho / SMTP Provider
                </a>
            </div>
        @endforelse
    </div>
@endsection
