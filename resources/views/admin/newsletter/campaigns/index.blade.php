@extends('admin.layout')

@section('title', 'Newsletter Campaigns')
@section('subtitle', 'Compose and broadcast bulk email campaigns to subscribed parents')

@section('actions')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.newsletter.subscribers.index') }}" class="btn">
            View Subscribers ({{ $subscriberCount }})
        </a>
        <a href="{{ route('admin.newsletter.campaigns.create') }}" class="btn primary">
            <x-admin.icon name="plus"/> Compose Campaign
        </a>
    </div>
@endsection

@section('content')
    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 text-[11px] uppercase font-bold text-slate-500 tracking-wider">
                    <th class="p-4">Subject</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Recipients</th>
                    <th class="p-4">Sent Count</th>
                    <th class="p-4">Sent At</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-xs">
                @forelse($campaigns as $c)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="p-4 font-bold text-slate-900 dark:text-white">
                            {{ $c->subject }}
                        </td>
                        <td class="p-4">
                            @if($c->status === 'sent')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">SENT</span>
                            @elseif($c->status === 'sending')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700">SENDING...</span>
                            @elseif($c->status === 'scheduled')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">SCHEDULED</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">DRAFT</span>
                            @endif
                        </td>
                        <td class="p-4 font-mono font-semibold">
                            {{ $c->recipient_count }}
                        </td>
                        <td class="p-4 font-mono text-emerald-600 font-bold">
                            {{ $c->sent_count }}
                        </td>
                        <td class="p-4 text-slate-500 text-[11px]">
                            {{ $c->sent_at ? $c->sent_at->format('M j, Y H:i') : 'Not sent' }}
                        </td>
                        <td class="p-4 text-right flex items-center justify-end gap-2">
                            @if($c->status === 'draft')
                                <form action="{{ route('admin.newsletter.campaigns.send-now', $c->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700 transition-colors">
                                        Send Now
                                    </button>
                                </form>
                                <a href="{{ route('admin.newsletter.campaigns.edit', $c->id) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200">
                                    Edit
                                </a>
                                <form action="{{ route('admin.newsletter.campaigns.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Delete campaign?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1.5 rounded-lg text-xs text-rose-600 hover:bg-rose-50">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400">No newsletter campaigns created yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
