@extends('admin.layout')
@section('title', 'Campaigns')
@section('actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.chatbot.campaigns.create') }}" class="btn-primary px-4 py-2 rounded-lg text-sm font-bold inline-flex items-center gap-1.5">
            + New Campaign
        </a>
    </div>
@endsection
@section('content')
<div class="space-y-4">
    @if(session('success'))
        <div class="px-4 py-3 rounded-xl text-sm" style="background:#dcfce7;color:#166534">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="px-4 py-3 rounded-xl text-sm" style="background:#fee2e2;color:#991b1b">{{ session('error') }}</div>
    @endif

    <div class="flex gap-2 mb-4 flex-wrap">
        <a href="{{ route('admin.chatbot.campaigns.index') }}" class="btn-sm {{ !request('status') ? 'btn-primary' : 'btn-secondary' }}">All</a>
        <a href="{{ route('admin.chatbot.campaigns.index', ['status' => 'draft']) }}" class="btn-sm {{ request('status') === 'draft' ? 'btn-primary' : 'btn-secondary' }}">Draft</a>
        <a href="{{ route('admin.chatbot.campaigns.index', ['status' => 'sending']) }}" class="btn-sm {{ request('status') === 'sending' ? 'btn-primary' : 'btn-secondary' }}">Sending</a>
        <a href="{{ route('admin.chatbot.campaigns.index', ['status' => 'sent']) }}" class="btn-sm {{ request('status') === 'sent' ? 'btn-primary' : 'btn-secondary' }}">Sent</a>
        <a href="{{ route('admin.chatbot.campaigns.index', ['status' => 'scheduled']) }}" class="btn-sm {{ request('status') === 'scheduled' ? 'btn-primary' : 'btn-secondary' }}">Scheduled</a>
        <a href="{{ route('admin.chatbot.campaigns.index', ['status' => 'completed']) }}" class="btn-sm {{ request('status') === 'completed' ? 'btn-primary' : 'btn-secondary' }}">Completed</a>
    </div>

    @if($campaigns->isEmpty())
        <div class="card p-12 text-center">
            <p class="text-sm" style="color:var(--text-muted)">No campaigns yet. Create your first campaign to get started.</p>
        </div>
    @else
        <div class="card p-0 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:1px solid var(--border)">
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Name</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Type</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Channel</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Status</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Sent / Delivered</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($campaigns as $campaign)
                        <tr style="border-bottom:1px solid var(--border)" class="hover:bg-surface-2/50 transition">
                            <td class="px-4 py-3 font-medium" style="color:var(--text)">{{ $campaign->name }}</td>
                            <td class="px-4 py-3"><span class="badge">{{ $campaign->type }}</span></td>
                            <td class="px-4 py-3" style="color:var(--text-muted)">{{ $campaign->channel ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $colors = ['draft' => '#f3f4f6;color:#6b7280', 'scheduled' => '#dbeafe;color:#1e40af', 'sending' => '#fef3c7;color:#92400e', 'sent' => '#dcfce7;color:#166534', 'completed' => '#dcfce7;color:#166534', 'cancelled' => '#fee2e2;color:#991b1b', 'failed' => '#fee2e2;color:#991b1b'];
                                    $c = $colors[$campaign->status] ?? '#f3f4f6;color:#6b7280';
                                @endphp
                                <span class="badge" style="background:{{ explode(';', $c)[0] }};color:{{ explode(';', $c)[1] }}">{{ ucfirst($campaign->status) }}</span>
                            </td>
                            <td class="px-4 py-3" style="color:var(--text-muted)">
                                {{ $campaign->sent_count ?? 0 }} / {{ $campaign->delivered_count ?? 0 }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.chatbot.campaigns.edit', $campaign) }}" class="text-sm font-medium hover:underline" style="color:var(--primary)">Edit</a>
                                    @if(in_array($campaign->status, ['draft', 'scheduled']))
                                        <form method="POST" action="{{ route('admin.chatbot.campaigns.send', $campaign) }}" style="display:inline">
                                            @csrf
                                            <button type="submit" class="text-sm font-medium hover:underline" style="color:#16a34a" onclick="return confirm('Send this campaign?')">Send</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.chatbot.campaigns.duplicate', $campaign) }}" style="display:inline">
                                        @csrf
                                        <button type="submit" class="text-sm font-medium hover:underline" style="color:#6b7280">Duplicate</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.chatbot.campaigns.destroy', $campaign) }}" onsubmit="return confirm('Delete this campaign?')" style="display:inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-sm font-medium hover:underline" style="color:#dc2626">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($campaigns->hasPages())
            <div class="p-4">{{ $campaigns->links() }}</div>
        @endif
    @endif
</div>
@endsection
