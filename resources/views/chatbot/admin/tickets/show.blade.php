@extends('admin.layouts.app')

@section('title', $ticket->subject)

@section('content')
<div style="padding:24px;max-width:1000px;margin:0 auto">
    <a href="{{ route('admin.chatbot.tickets.index') }}" style="color:#64748b;text-decoration:none;font-size:13px;margin-bottom:16px;display:inline-block">&larr; Back to tickets</a>

    @if(session('success'))
        <div style="padding:12px 16px;background:#dcfce7;color:#166534;border-radius:8px;margin-bottom:16px;font-size:14px">{{ session('success') }}</div>
    @endif

    <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:24px">
        <div>
            <div style="font-size:12px;color:#64748b;font-weight:600">{{ $ticket->ticket_number }}</div>
            <h1 style="font-size:24px;font-weight:800;margin:4px 0">{{ $ticket->subject }}</h1>
            @if($ticket->description)
                <p style="color:#475569;font-size:14px;margin:8px 0 0;white-space:pre-wrap">{{ $ticket->description }}</p>
            @endif
        </div>
        <div style="display:flex;gap:8px">
            <a href="{{ route('admin.chatbot.tickets.edit', $ticket) }}" style="padding:8px 16px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;color:#0b2545;text-decoration:none;font-weight:600;font-size:12px">Edit</a>
            <form method="POST" action="{{ route('admin.chatbot.tickets.destroy', $ticket) }}" onsubmit="return confirm('Delete this ticket?')" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" style="padding:8px 16px;border:1px solid #ef4444;border-radius:8px;background:#fff;color:#ef4444;cursor:pointer;font-weight:600;font-size:12px">Delete</button>
            </form>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:24px">
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">Status</div>
            <div style="font-size:14px;font-weight:600;margin-top:4px;text-transform:capitalize">{{ $ticket->status }}</div>
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">Priority</div>
            <div style="font-size:14px;font-weight:600;margin-top:4px;text-transform:capitalize">{{ $ticket->priority }}</div>
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">Department</div>
            <div style="font-size:14px;font-weight:600;margin-top:4px">{{ $ticket->department?->name ?? '—' }}</div>
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">Assigned To</div>
            <div style="font-size:14px;font-weight:600;margin-top:4px">{{ $ticket->assignedAgent?->name ?? 'Unassigned' }}</div>
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">SLA</div>
            <div style="font-size:14px;font-weight:600;margin-top:4px">{{ $ticket->sla_minutes ? $ticket->sla_minutes . ' min' : 'None' }} {{ $ticket->sla_breached ? '(Breached)' : '' }}</div>
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">Response Time</div>
            <div style="font-size:14px;font-weight:600;margin-top:4px">{{ $ticket->response_time_seconds ? gmdate('H:i:s', $ticket->response_time_seconds) : '—' }}</div>
        </div>
    </div>

    <div style="display:flex;gap:8px;margin-bottom:24px;flex-wrap:wrap">
        <form method="POST" action="{{ route('admin.chatbot.tickets.status', $ticket) }}" style="display:inline">
            @csrf
            <input type="hidden" name="status" value="open">
            <button type="submit" style="padding:6px 14px;border:1px solid #16a34a;border-radius:6px;background:#fff;color:#16a34a;cursor:pointer;font-weight:600;font-size:12px">Reopen</button>
        </form>
        <form method="POST" action="{{ route('admin.chatbot.tickets.status', $ticket) }}" style="display:inline">
            @csrf
            <input type="hidden" name="status" value="resolved">
            <button type="submit" style="padding:6px 14px;border:1px solid #6366f1;border-radius:6px;background:#fff;color:#6366f1;cursor:pointer;font-weight:600;font-size:12px">Resolve</button>
        </form>
        <form method="POST" action="{{ route('admin.chatbot.tickets.status', $ticket) }}" style="display:inline">
            @csrf
            <input type="hidden" name="status" value="closed">
            <button type="submit" style="padding:6px 14px;border:1px solid #64748b;border-radius:6px;background:#fff;color:#64748b;cursor:pointer;font-weight:600;font-size:12px">Close</button>
        </form>
        <form method="POST" action="{{ route('admin.chatbot.tickets.assign', $ticket) }}" style="display:inline-flex;gap:6px;align-items:center">
            @csrf
            <select name="assigned_agent_id" style="padding:6px 10px;border:1px solid #e2e8f0;border-radius:6px;font-size:12px">
                <option value="">Unassign</option>
                @foreach(\App\Models\User::all() as $a)
                    <option value="{{ $a->id }}" {{ $ticket->assigned_agent_id == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                @endforeach
            </select>
            <button type="submit" style="padding:6px 14px;border:1px solid #e2e8f0;border-radius:6px;background:#fff;color:#0b2545;cursor:pointer;font-weight:600;font-size:12px">Assign</button>
        </form>
    </div>

    <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;padding:16px;margin-bottom:24px">
        <h3 style="font-size:14px;font-weight:700;margin:0 0 16px">Replies ({{ $ticket->replies->count() }})</h3>

        <form method="POST" action="{{ route('admin.chatbot.tickets.reply', $ticket) }}" style="margin-bottom:16px">
            @csrf
            <textarea name="body" rows="3" placeholder="Type your reply..." required style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;margin-bottom:8px"></textarea>
            <div style="display:flex;gap:8px;align-items:center">
                <button type="submit" style="padding:8px 20px;border:none;border-radius:8px;background:#0b2545;color:#fff;font-weight:600;cursor:pointer;font-size:12px">Send Reply</button>
                <label style="display:flex;align-items:center;gap:4px;font-size:12px;color:#64748b;cursor:pointer">
                    <input type="checkbox" name="is_internal" value="1"> Internal note
                </label>
                <label style="display:flex;align-items:center;gap:4px;font-size:12px;color:#64748b;cursor:pointer">
                    <input type="checkbox" name="is_solution" value="1"> Mark as solution
                </label>
            </div>
        </form>

        @foreach($ticket->replies->sortByDesc('created_at') as $reply)
            <div style="padding:12px;background:{{ $reply->is_internal ? '#fefce8' : '#f8fafc' }};border-radius:8px;margin-bottom:8px;border-left:3px solid {{ $reply->is_internal ? '#eab308' : ($reply->is_solution ? '#16a34a' : '#e2e8f0') }}">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
                    <div style="display:flex;align-items:center;gap:6px">
                        <strong style="font-size:13px">{{ $reply->user?->name ?? 'System' }}</strong>
                        @if($reply->is_internal)<span style="background:#fef3c7;color:#92400e;padding:1px 6px;border-radius:4px;font-size:10px;font-weight:600">Internal</span>@endif
                        @if($reply->is_solution)<span style="background:#dcfce7;color:#166534;padding:1px 6px;border-radius:4px;font-size:10px;font-weight:600">Solution</span>@endif
                    </div>
                    <span style="font-size:11px;color:#94a3b8">{{ $reply->created_at->diffForHumans() }}</span>
                </div>
                <p style="font-size:13px;color:#334155;margin:0;white-space:pre-wrap">{{ $reply->body }}</p>
            </div>
        @endforeach
    </div>
</div>
@endsection
