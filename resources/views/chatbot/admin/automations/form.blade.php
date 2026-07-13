@extends('admin.layouts.app')

@section('title', isset($automation) ? 'Edit Automation' : 'Create Automation')

@section('content')
<div style="padding:24px;max-width:800px;margin:0 auto">
    <a href="{{ route('admin.chatbot.automations.index') }}" style="color:#64748b;text-decoration:none;font-size:13px;margin-bottom:16px;display:inline-block">&larr; Back to automations</a>

    <h1 style="font-size:24px;font-weight:800;margin:0 0 24px">{{ isset($automation) ? 'Edit Automation' : 'Create Automation' }}</h1>

    <form method="POST" action="{{ isset($automation) ? route('admin.chatbot.automations.update', $automation) : route('admin.chatbot.automations.store') }}" style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;padding:24px">
        @csrf
        @if(isset($automation)) @method('PUT') @endif

        <div style="margin-bottom:16px">
            <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Name</label>
            <input type="text" name="name" value="{{ old('name', $automation->name ?? '') }}" required style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px">
        </div>

        <div style="margin-bottom:16px">
            <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Description</label>
            <textarea name="description" rows="3" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px">{{ old('description', $automation->description ?? '') }}</textarea>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
            <div>
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Trigger Type</label>
                <select name="trigger_type" required style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px">
                    <option value="">Select trigger...</option>
                    @foreach($triggers as $t)
                        <option value="{{ $t }}" {{ old('trigger_type', $automation->trigger_type ?? '') === $t ? 'selected' : '' }}>{{ str_replace('_', ' ', ucfirst($t)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Status</label>
                <select name="status" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px">
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ old('status', $automation->status ?? 'draft') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
            <div>
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Priority</label>
                <input type="number" name="priority" value="{{ old('priority', $automation->priority ?? 0) }}" min="0" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px">
            </div>
            <div>
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Max Executions (0 = unlimited)</label>
                <input type="number" name="max_executions" value="{{ old('max_executions', $automation->max_executions ?? 0) }}" min="0" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px">
            </div>
        </div>

        <div style="margin-bottom:16px">
            <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Trigger Config (JSON)</label>
            <textarea name="trigger_config" rows="4" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:monospace">{{ old('trigger_config', is_string($automation->trigger_config ?? '') ? ($automation->trigger_config ?? '') : json_encode($automation->trigger_config ?? '', JSON_PRETTY_PRINT)) }}</textarea>
        </div>

        <div style="margin-bottom:16px">
            <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Conditions (JSON)</label>
            <textarea name="conditions" rows="6" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:monospace">{{ old('conditions', is_string($automation->conditions ?? '') ? ($automation->conditions ?? '') : json_encode($automation->conditions ?? '', JSON_PRETTY_PRINT)) }}</textarea>
            <p style="font-size:11px;color:#94a3b8;margin:4px 0 0">Format: {"operator":"and","rules":[{"field":"priority","operator":"eq","value":"high"}]}</p>
        </div>

        <div style="margin-bottom:16px">
            <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Actions (JSON) <span style="color:#dc2626">*</span></label>
            <textarea name="actions" rows="8" required style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:monospace">{{ old('actions', is_string($automation->actions ?? '') ? ($automation->actions ?? '') : json_encode($automation->actions ?? '', JSON_PRETTY_PRINT)) }}</textarea>
            <p style="font-size:11px;color:#94a3b8;margin:4px 0 0">Format: [{"type":"send_email","config":{"to":"admin@example.com","subject":"Alert","body":"..."}}]</p>
        </div>

        <div style="margin-bottom:24px">
            <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Schedule (JSON, optional)</label>
            <textarea name="schedule" rows="3" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:monospace">{{ old('schedule', is_string($automation->schedule ?? '') ? ($automation->schedule ?? '') : json_encode($automation->schedule ?? '', JSON_PRETTY_PRINT)) }}</textarea>
        </div>

        <button type="submit" style="padding:12px 24px;border:none;border-radius:8px;background:#0b2545;color:#fff;font-weight:600;cursor:pointer;font-size:14px;width:100%">{{ isset($automation) ? 'Update Automation' : 'Create Automation' }}</button>
    </form>
</div>
@endsection