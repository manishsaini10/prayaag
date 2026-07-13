@extends('admin.layouts.app')

@section('title', isset($ticket) ? 'Edit Ticket' : 'Create Ticket')

@section('content')
<div style="padding:24px;max-width:700px;margin:0 auto">
    <a href="{{ route('admin.chatbot.tickets.index') }}" style="color:#64748b;text-decoration:none;font-size:13px;margin-bottom:16px;display:inline-block">&larr; Back to tickets</a>

    <h1 style="font-size:24px;font-weight:800;margin:0 0 24px">{{ isset($ticket) ? 'Edit Ticket' : 'Create Ticket' }}</h1>

    <form method="POST" action="{{ isset($ticket) ? route('admin.chatbot.tickets.update', $ticket) : route('admin.chatbot.tickets.store') }}" style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;padding:24px">
        @csrf
        @if(isset($ticket)) @method('PUT') @endif

        <div style="margin-bottom:16px">
            <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Subject</label>
            <input type="text" name="subject" value="{{ old('subject', $ticket->subject ?? '') }}" required style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px">
        </div>

        <div style="margin-bottom:16px">
            <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Description</label>
            <textarea name="description" rows="4" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px">{{ old('description', $ticket->description ?? '') }}</textarea>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
            <div>
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Priority</label>
                <select name="priority" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px">
                    <option value="low" {{ old('priority', $ticket->priority ?? '') === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority', $ticket->priority ?? 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ old('priority', $ticket->priority ?? '') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ old('priority', $ticket->priority ?? '') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="critical" {{ old('priority', $ticket->priority ?? '') === 'critical' ? 'selected' : '' }}>Critical</option>
                </select>
            </div>
            <div>
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Status</label>
                <select name="status" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px">
                    <option value="open" {{ old('status', $ticket->status ?? 'open') === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="pending" {{ old('status', $ticket->status ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="resolved" {{ old('status', $ticket->status ?? '') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ old('status', $ticket->status ?? '') === 'closed' ? 'selected' : '' }}>Closed</option>
                    <option value="on-hold" {{ old('status', $ticket->status ?? '') === 'on-hold' ? 'selected' : '' }}>On Hold</option>
                </select>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
            <div>
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Department</label>
                <select name="department_id" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px">
                    <option value="">No department</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ old('department_id', $ticket->department_id ?? '') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Assigned Agent</label>
                <select name="assigned_agent_id" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px">
                    <option value="">Unassigned</option>
                    @foreach($agents as $a)
                        <option value="{{ $a->id }}" {{ old('assigned_agent_id', $ticket->assigned_agent_id ?? '') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">
            <div>
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Category</label>
                <input type="text" name="category" value="{{ old('category', $ticket->category ?? '') }}" placeholder="e.g. billing, support" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px">
            </div>
            <div>
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">SLA Target (minutes)</label>
                <input type="number" name="sla_minutes" value="{{ old('sla_minutes', $ticket->sla_minutes ?? '') }}" min="0" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px">
            </div>
        </div>

        <button type="submit" style="padding:12px 24px;border:none;border-radius:8px;background:#0b2545;color:#fff;font-weight:600;cursor:pointer;font-size:14px;width:100%">{{ isset($ticket) ? 'Update Ticket' : 'Create Ticket' }}</button>
    </form>
</div>
@endsection
