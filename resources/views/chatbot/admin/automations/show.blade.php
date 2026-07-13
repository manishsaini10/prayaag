@extends('admin.layouts.app')

@section('title', $automation->name)

@section('content')
<div style="padding:24px;max-width:1000px;margin:0 auto">
    <a href="{{ route('admin.chatbot.automations.index') }}" style="color:#64748b;text-decoration:none;font-size:13px;margin-bottom:16px;display:inline-block">&larr; Back to automations</a>

    <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:24px">
        <div>
            <h1 style="font-size:24px;font-weight:800;margin:0">{{ $automation->name }}</h1>
            <p style="color:#64748b;margin:4px 0 0">{{ $automation->triggerLabel() }} &middot; {{ $automation->execution_count }} executions</p>
        </div>
        <div style="display:flex;gap:8px">
            <form method="POST" action="{{ route('admin.chatbot.automations.test', $automation) }}" style="display:inline">
                @csrf
                <button type="submit" style="padding:8px 16px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;color:#0b2545;font-weight:600;cursor:pointer;font-size:12px">Test Run</button>
            </form>
            <a href="{{ route('admin.chatbot.automations.edit', $automation) }}" style="padding:8px 16px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;color:#0b2545;text-decoration:none;font-weight:600;font-size:12px">Edit</a>
        </div>
    </div>

    @if(session('success'))
        <div style="padding:12px 16px;background:#dcfce7;color:#166534;border-radius:8px;margin-bottom:16px;font-size:14px">{{ session('success') }}</div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">
        <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;padding:20px">
            <h3 style="font-size:14px;font-weight:700;margin:0 0 12px;color:#0b2545">Details</h3>
            <table style="width:100%;font-size:13px">
                <tr><td style="padding:6px 0;color:#64748b;width:120px">Status</td><td style="font-weight:600"><span style="background:{{ $automation->status === 'active' ? '#dcfce7' : '#f1f5f9' }};color:{{ $automation->status === 'active' ? '#166534' : '#64748b' }};padding:2px 8px;border-radius:4px;font-size:11px">{{ ucfirst($automation->status) }}</span></td></tr>
                <tr><td style="padding:6px 0;color:#64748b">Active</td><td style="font-weight:600">{{ $automation->is_active ? 'Yes' : 'No' }}</td></tr>
                <tr><td style="padding:6px 0;color:#64748b">Trigger</td><td style="font-weight:600">{{ $automation->triggerLabel() }}</td></tr>
                <tr><td style="padding:6px 0;color:#64748b">Priority</td><td style="font-weight:600">{{ $automation->priority }}</td></tr>
                <tr><td style="padding:6px 0;color:#64748b">Executions</td><td style="font-weight:600">{{ $automation->execution_count }}{{ $automation->max_executions > 0 ? ' / ' . $automation->max_executions : ' (unlimited)' }}</td></tr>
                <tr><td style="padding:6px 0;color:#64748b">Last Run</td><td style="font-weight:600">{{ $automation->last_run_at?->diffForHumans() ?? 'Never' }}</td></tr>
                <tr><td style="padding:6px 0;color:#64748b">Created</td><td style="font-weight:600">{{ $automation->created_at->diffForHumans() }}</td></tr>
            </table>
        </div>

        <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;padding:20px">
            <h3 style="font-size:14px;font-weight:700;margin:0 0 12px;color:#0b2545">Description</h3>
            <p style="font-size:13px;color:#475569;margin:0;line-height:1.6">{{ $automation->description ?? 'No description provided.' }}</p>
        </div>
    </div>

    <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;padding:20px;margin-bottom:16px">
        <h3 style="font-size:14px;font-weight:700;margin:0 0 12px;color:#0b2545">Trigger Config</h3>
        <pre style="background:#f8fafc;padding:12px;border-radius:8px;font-size:12px;overflow-x:auto;margin:0">{{ json_encode($automation->trigger_config, JSON_PRETTY_PRINT) ?: 'None' }}</pre>
    </div>

    <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;padding:20px;margin-bottom:16px">
        <h3 style="font-size:14px;font-weight:700;margin:0 0 12px;color:#0b2545">Conditions</h3>
        <pre style="background:#f8fafc;padding:12px;border-radius:8px;font-size:12px;overflow-x:auto;margin:0">{{ json_encode($automation->conditions, JSON_PRETTY_PRINT) ?: 'None (always runs)' }}</pre>
    </div>

    <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;padding:20px;margin-bottom:24px">
        <h3 style="font-size:14px;font-weight:700;margin:0 0 12px;color:#0b2545">Actions</h3>
        <pre style="background:#f8fafc;padding:12px;border-radius:8px;font-size:12px;overflow-x:auto;margin:0">{{ json_encode($automation->actions, JSON_PRETTY_PRINT) }}</pre>
    </div>

    <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden">
        <div style="padding:20px;border-bottom:1px solid #e2e8f0">
            <h3 style="font-size:14px;font-weight:700;margin:0;color:#0b2545">Execution Logs ({{ $logs->total() }})</h3>
        </div>
        @forelse($logs as $log)
            <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9">
                <div style="display:flex;justify-content:space-between;align-items:start">
                    <div>
                        <span style="font-weight:600;font-size:13px">{{ $log->trigger_event }}</span>
                        <span style="background:{{ $log->status === 'completed' ? '#dcfce7' : '#fee2e2' }};color:{{ $log->status === 'completed' ? '#166534' : '#991b1b' }};padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;margin-left:8px">{{ ucfirst($log->status) }}</span>
                        @if($log->conditions_met)
                            <span style="background:#e0e7ff;color:#4338ca;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;margin-left:4px">Conditions met</span>
                        @endif
                    </div>
                    <span style="font-size:11px;color:#64748b">{{ $log->created_at->diffForHumans() }} ({{ $log->duration_ms }}ms)</span>
                </div>
                @if($log->error_message)
                    <p style="font-size:12px;color:#dc2626;margin:8px 0 0;background:#fef2f2;padding:8px;border-radius:4px">{{ $log->error_message }}</p>
                @endif
                @if($log->executed_actions)
                    <pre style="background:#f8fafc;padding:8px;border-radius:4px;font-size:11px;overflow-x:auto;margin:8px 0 0">{{ json_encode($log->executed_actions, JSON_PRETTY_PRINT) }}</pre>
                @endif
            </div>
        @empty
            <div style="padding:40px;text-align:center;color:#94a3b8;font-size:14px">No execution logs yet. Click "Test Run" to trigger this automation.</div>
        @endforelse
    </div>
    <div style="margin-top:16px">{{ $logs->links() }}</div>
</div>
@endsection