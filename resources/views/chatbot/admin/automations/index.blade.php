@extends('admin.layouts.app')

@section('title', 'Automations')

@section('content')
<div style="padding:24px;max-width:1200px;margin:0 auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
        <div>
            <h1 style="font-size:24px;font-weight:800;margin:0">Automations</h1>
            <p style="color:#64748b;margin:4px 0 0">{{ $automations->total() }} automation rules</p>
        </div>
        <a href="{{ route('admin.chatbot.automations.create') }}" style="padding:8px 16px;border:none;border-radius:8px;background:#0b2545;color:#fff;text-decoration:none;font-weight:600">+ Automation</a>
    </div>

    @if(session('success'))
        <div style="padding:12px 16px;background:#dcfce7;color:#166534;border-radius:8px;margin-bottom:16px;font-size:14px">{{ session('success') }}</div>
    @endif

    <div style="display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap">
        <select onchange="if(this.value) window.location=this.value" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
            <option value="{{ route('admin.chatbot.automations.index') }}">All Triggers</option>
            @foreach($triggers as $t)
                <option value="{{ route('admin.chatbot.automations.index') }}?trigger_type={{ $t }}" {{ request('trigger_type') === $t ? 'selected' : '' }}>{{ str_replace('_', ' ', ucfirst($t)) }}</option>
            @endforeach
        </select>
        <select onchange="if(this.value) window.location=this.value" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
            <option value="{{ route('admin.chatbot.automations.index') }}">All Status</option>
            @foreach($statuses as $s)
                <option value="{{ route('admin.chatbot.automations.index') }}?status={{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>

    <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden">
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#f8fafc;text-align:left">
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Name</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Trigger</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Priority</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Status</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Active</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Executions</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Last Run</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($automations as $a)
                    <tr style="border-top:1px solid #f1f5f9">
                        <td style="padding:12px 16px;font-weight:600;font-size:13px">{{ $a->name }}</td>
                        <td style="padding:12px 16px;font-size:12px;color:#64748b">{{ $a->triggerLabel() }}</td>
                        <td style="padding:12px 16px;font-size:12px;color:#64748b">{{ $a->priority }}</td>
                        <td style="padding:12px 16px">
                            <span style="background:{{ $a->status === 'active' ? '#dcfce7' : ($a->status === 'paused' ? '#fef3c7' : ($a->status === 'draft' ? '#e0e7ff' : ($a->status === 'completed' ? '#dbeafe' : '#f1f5f9'))) }};color:{{ $a->status === 'active' ? '#166534' : ($a->status === 'paused' ? '#92400e' : ($a->status === 'draft' ? '#4338ca' : ($a->status === 'completed' ? '#1e40af' : '#64748b'))) }};padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;text-transform:capitalize">{{ $a->status }}</span>
                        </td>
                        <td style="padding:12px 16px">
                            <span style="background:{{ $a->is_active ? '#dcfce7' : '#f1f5f9' }};color:{{ $a->is_active ? '#166534' : '#64748b' }};padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600">{{ $a->is_active ? 'Yes' : 'No' }}</span>
                        </td>
                        <td style="padding:12px 16px;font-size:12px;color:#64748b">{{ $a->execution_count }}{{ $a->max_executions > 0 ? '/' . $a->max_executions : '' }}</td>
                        <td style="padding:12px 16px;font-size:12px;color:#64748b">{{ $a->last_run_at?->diffForHumans() ?? '—' }}</td>
                        <td style="padding:12px 16px">
                            <div style="display:flex;gap:6px">
                                <a href="{{ route('admin.chatbot.automations.show', $a) }}" style="color:#2563eb;text-decoration:none;font-size:12px;font-weight:600">View</a>
                                <a href="{{ route('admin.chatbot.automations.edit', $a) }}" style="color:#64748b;text-decoration:none;font-size:12px">Edit</a>
                                <form method="POST" action="{{ route('admin.chatbot.automations.toggle', $a) }}" style="display:inline" onsubmit="return confirm('{{ $a->is_active ? 'Deactivate' : 'Activate' }} this automation?')">
                                    @csrf
                                    <button type="submit" style="background:none;border:none;color:{{ $a->is_active ? '#dc2626' : '#16a34a' }};cursor:pointer;font-size:12px;padding:0;text-decoration:underline">{{ $a->is_active ? 'Deactivate' : 'Activate' }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="padding:40px;text-align:center;color:#94a3b8;font-size:14px">No automation rules found. <a href="{{ route('admin.chatbot.automations.create') }}" style="color:#2563eb">Create one</a>.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px">{{ $automations->links() }}</div>
</div>
@endsection