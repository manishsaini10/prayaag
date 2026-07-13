@extends('admin.layouts.app')

@section('title', 'Visitor Detail')

@section('content')
<div style="padding:24px;max-width:1000px;margin:0 auto">
    <a href="{{ route('admin.chatbot.visitors.index') }}" style="color:#64748b;text-decoration:none;font-size:13px;margin-bottom:16px;display:inline-block">&larr; Back to visitors</a>

    <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:24px">
        <div>
            <h1 style="font-size:24px;font-weight:800;margin:0">{{ $visitor->name ?? 'Anonymous Visitor' }}</h1>
            <p style="color:#64748b;margin:4px 0 0">ID: {{ $visitor->session_id }}</p>
        </div>
        <div style="display:flex;gap:8px">
            <form method="POST" action="{{ route('admin.chatbot.visitors.delete', $visitor) }}" onsubmit="return confirm('Delete this visitor and all data?')" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" style="padding:8px 16px;border:1px solid #ef4444;border-radius:8px;background:#fff;color:#ef4444;cursor:pointer;font-weight:600;font-size:12px">Delete</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div style="padding:12px 16px;background:#dcfce7;color:#166534;border-radius:8px;margin-bottom:16px;font-size:14px">{{ session('success') }}</div>
    @endif

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;margin-bottom:24px">
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">IP Address</div>
            <div style="font-size:14px;font-weight:600;margin-top:4px">{{ $visitor->ip_address ?? '—' }}</div>
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">Location</div>
            <div style="font-size:14px;font-weight:600;margin-top:4px">{{ $visitor->country ?? '—' }}{{ $visitor->city ? ", {$visitor->city}" : '' }}</div>
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">Device / Browser</div>
            <div style="font-size:14px;font-weight:600;margin-top:4px">{{ $visitor->device ?? '—' }} / {{ $visitor->browser ?? '—' }}</div>
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">First Seen</div>
            <div style="font-size:14px;font-weight:600;margin-top:4px">{{ $visitor->created_at->format('M j, Y g:i A') }}</div>
        </div>
    </div>

    @if($visitor->email || $visitor->phone)
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:24px">
            <h3 style="font-size:14px;font-weight:700;margin:0 0 8px">Contact Info</h3>
            <div style="font-size:13px;color:#64748b">
                @if($visitor->email)<div>Email: <strong>{{ $visitor->email }}</strong></div>@endif
                @if($visitor->phone)<div>Phone: <strong>{{ $visitor->phone }}</strong></div>@endif
                @if($visitor->name)<div>Name: <strong>{{ $visitor->name }}</strong></div>@endif
            </div>
        </div>
    @endif

    <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;padding:16px;margin-bottom:24px">
        <h3 style="font-size:14px;font-weight:700;margin:0 0 12px">Activity Timeline</h3>
        @if(count($timeline) > 0)
            <div style="position:relative;padding-left:20px">
                <div style="position:absolute;left:8px;top:0;bottom:0;width:2px;background:#e2e8f0"></div>
                @foreach($timeline as $entry)
                    <div style="position:relative;padding:0 0 16px 16px">
                        <div style="position:absolute;left:-16px;top:4px;width:10px;height:10px;border-radius:50%;background:{{ $entry['type'] === 'page_view' ? '#2563eb' : ($entry['type'] === 'conversation' ? '#16a34a' : '#f59e0b') }};border:2px solid #fff"></div>
                        <div style="font-size:12px;font-weight:600">{{ $entry['label'] }}</div>
                        @if(isset($entry['url']))<div style="font-size:11px;color:#64748b">{{ $entry['url'] }}</div>@endif
                        @if(isset($entry['status']))<div style="font-size:11px;color:#64748b">Status: {{ $entry['status'] }}</div>@endif
                        <div style="font-size:10px;color:#94a3b8;margin-top:2px">{{ \Carbon\Carbon::parse($entry['time'])->diffForHumans() }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <p style="color:#94a3b8;font-size:13px">No activity recorded yet.</p>
        @endif
    </div>

    @if($visitor->leads->count() > 0)
        <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;padding:16px;margin-bottom:24px">
            <h3 style="font-size:14px;font-weight:700;margin:0 0 12px">Leads</h3>
            @foreach($visitor->leads as $lead)
                <div style="padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:13px">
                    <strong>{{ $lead->name }}</strong> — {{ $lead->email ?? '—' }} / {{ $lead->phone ?? '—' }}
                    <span style="font-size:11px;color:#64748b;margin-left:8px">{{ $lead->created_at->diffForHumans() }}</span>
                </div>
            @endforeach
        </div>
    @endif

    @if($visitor->conversations->count() > 0)
        <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;padding:16px">
            <h3 style="font-size:14px;font-weight:700;margin:0 0 12px">Conversations</h3>
            @foreach($visitor->conversations as $conv)
                <div style="padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:13px;display:flex;justify-content:space-between">
                    <span>#{{ $conv->short_id }} — <span style="color:#64748b">{{ $conv->messages_count ?? 0 }} messages</span></span>
                    <span style="font-size:11px;color:#64748b">{{ $conv->status }} &middot; {{ $conv->created_at->diffForHumans() }}</span>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
