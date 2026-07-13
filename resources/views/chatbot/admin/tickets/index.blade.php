@extends('admin.layouts.app')

@section('title', 'Tickets')

@section('content')
<div style="padding:24px;max-width:1200px;margin:0 auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
        <div>
            <h1 style="font-size:24px;font-weight:800;margin:0">Tickets</h1>
            <p style="color:#64748b;margin:4px 0 0">{{ $tickets->total() }} tickets</p>
        </div>
        <div style="display:flex;gap:8px">
            <a href="{{ route('admin.chatbot.canned.index') }}" style="padding:8px 16px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;color:#0b2545;text-decoration:none;font-weight:600;font-size:12px">Canned Responses</a>
            <a href="{{ route('admin.chatbot.tickets.create') }}" style="padding:8px 16px;border:none;border-radius:8px;background:#0b2545;color:#fff;text-decoration:none;font-weight:600">+ Ticket</a>
        </div>
    </div>

    @if(session('success'))
        <div style="padding:12px 16px;background:#dcfce7;color:#166534;border-radius:8px;margin-bottom:16px;font-size:14px">{{ session('success') }}</div>
    @endif

    <div style="display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap">
        <select onchange="if(this.value) window.location=this.value" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
            <option value="{{ route('admin.chatbot.tickets.index') }}">All Status</option>
            <option value="{{ route('admin.chatbot.tickets.index') }}?status=open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
            <option value="{{ route('admin.chatbot.tickets.index') }}?status=pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="{{ route('admin.chatbot.tickets.index') }}?status=resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
            <option value="{{ route('admin.chatbot.tickets.index') }}?status=closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
            <option value="{{ route('admin.chatbot.tickets.index') }}?status=on-hold" {{ request('status') === 'on-hold' ? 'selected' : '' }}>On Hold</option>
        </select>
        <select onchange="if(this.value) window.location=this.value" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
            <option value="{{ route('admin.chatbot.tickets.index') }}">All Priority</option>
            <option value="{{ route('admin.chatbot.tickets.index') }}?priority=critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
            <option value="{{ route('admin.chatbot.tickets.index') }}?priority=urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
            <option value="{{ route('admin.chatbot.tickets.index') }}?priority=high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
            <option value="{{ route('admin.chatbot.tickets.index') }}?priority=medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
            <option value="{{ route('admin.chatbot.tickets.index') }}?priority=low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
        </select>
        <select onchange="if(this.value) window.location=this.value" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
            <option value="{{ route('admin.chatbot.tickets.index') }}">All Departments</option>
            @foreach($departments as $d)
                <option value="{{ route('admin.chatbot.tickets.index') }}?department_id={{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
            @endforeach
        </select>
    </div>

    <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden">
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#f8fafc;text-align:left">
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Ticket</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Subject</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Priority</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Status</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Department</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Agent</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">SLA</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Created</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $t)
                    @php $t->checkSla(); @endphp
                    <tr style="border-top:1px solid #f1f5f9">
                        <td style="padding:12px 16px;font-size:12px;font-weight:600;color:#64748b">{{ $t->ticket_number }}</td>
                        <td style="padding:12px 16px;font-weight:600;font-size:13px">{{ $t->subject }}</td>
                        <td style="padding:12px 16px">
                            <span style="background:{{ $t->priority === 'critical' ? '#dc2626' : ($t->priority === 'urgent' ? '#ea580c' : ($t->priority === 'high' ? '#f59e0b' : ($t->priority === 'medium' ? '#3b82f6' : '#64748b'))) }};color:#fff;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600">{{ $t->priority }}</span>
                        </td>
                        <td style="padding:12px 16px">
                            <span style="background:{{ $t->status === 'open' ? '#dcfce7' : ($t->status === 'pending' ? '#fef3c7' : ($t->status === 'resolved' ? '#e0e7ff' : ($t->status === 'closed' ? '#f1f5f9' : '#fce7f3'))) }};color:{{ $t->status === 'open' ? '#166534' : ($t->status === 'pending' ? '#92400e' : ($t->status === 'resolved' ? '#4338ca' : ($t->status === 'closed' ? '#64748b' : '#9d174d'))) }};padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;text-transform:capitalize">{{ $t->status }}</span>
                        </td>
                        <td style="padding:12px 16px;font-size:13px;color:#64748b">{{ $t->department?->name ?? '—' }}</td>
                        <td style="padding:12px 16px;font-size:13px;color:#64748b">{{ $t->assignedAgent?->name ?? 'Unassigned' }}</td>
                        <td style="padding:12px 16px">
                            @if($t->sla_minutes)
                                <span style="background:{{ $t->sla_breached ? '#fee2e2' : '#fef3c7' }};color:{{ $t->sla_breached ? '#991b1b' : '#92400e' }};padding:2px 6px;border-radius:4px;font-size:11px;font-weight:600">{{ $t->sla_breached ? 'Breached' : $t->sla_minutes . 'm' }}</span>
                            @else
                                <span style="color:#94a3b8;font-size:11px">—</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px;font-size:12px;color:#64748b">{{ $t->created_at->diffForHumans() }}</td>
                        <td style="padding:12px 16px">
                            <a href="{{ route('admin.chatbot.tickets.show', $t) }}" style="color:#2563eb;text-decoration:none;font-size:12px;font-weight:600">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" style="padding:40px;text-align:center;color:#94a3b8;font-size:14px">No tickets found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px">{{ $tickets->links() }}</div>
</div>
@endsection
