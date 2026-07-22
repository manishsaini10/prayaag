@extends('admin.layout')

@section('title', 'Audit Entry Details')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold tracking-tight" style="color:var(--text)">Audit Activity Details</h2>
            <p class="text-xs" style="color:var(--text-muted)">Detailed difference view and system attributes of this log.</p>
        </div>
        <a href="{{ route('admin.audit.index') }}" class="btn btn-sm border px-3 py-1.5 rounded-lg text-xs font-semibold" style="border-color:var(--border); color:var(--text)">
            Back to List
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Metadata Card --}}
        <div class="card p-5 border rounded-xl space-y-4" style="border-color:var(--border)">
            <h3 class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted)">Audit Metadata</h3>
            
            <div>
                <span class="block text-xs" style="color:var(--text-muted)">Activity ID</span>
                <span class="font-mono text-xs font-semibold" style="color:var(--text)">{{ $log->id }}</span>
            </div>
            <div>
                <span class="block text-xs" style="color:var(--text-muted)">Module (Table)</span>
                <span class="font-semibold text-sm capitalize" style="color:var(--text)">{{ $log->log_name }}</span>
            </div>
            <div>
                <span class="block text-xs" style="color:var(--text-muted)">Description</span>
                <span class="font-semibold text-sm" style="color:var(--text)">{{ $log->description }}</span>
            </div>
            <div>
                <span class="block text-xs" style="color:var(--text-muted)">Subject ID & Class</span>
                <span class="font-mono text-xs font-semibold block" style="color:var(--text)">{{ $log->subject_id ?: 'N/A' }}</span>
                <span class="text-[10px] block" style="color:var(--text-muted)">{{ $log->subject_type ?: 'N/A' }}</span>
            </div>
            <div>
                <span class="block text-xs" style="color:var(--text-muted)">Causer ID & Class</span>
                <span class="font-mono text-xs font-semibold block" style="color:var(--text)">{{ $log->causer_id ?: 'System / Anonymous' }}</span>
                <span class="text-[10px] block" style="color:var(--text-muted)">{{ $log->causer_type ?: 'N/A' }}</span>
            </div>
            <div>
                <span class="block text-xs" style="color:var(--text-muted)">Timestamp</span>
                <span class="font-semibold text-sm" style="color:var(--text)">{{ $log->created_at->format('M j, Y H:i:s') }}</span>
            </div>
        </div>

        {{-- Changes / Attributes diff viewer --}}
        <div class="lg:col-span-2 card p-5 border rounded-xl" style="border-color:var(--border)">
            <h3 class="text-xs font-semibold uppercase tracking-wider mb-4" style="color:var(--text-muted)">Payload Difference Log</h3>

            @if(isset($log->properties['old']) && isset($log->properties['attributes']))
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b" style="border-color:var(--border); color:var(--text-muted)">
                                <th class="py-2 font-semibold w-1/3">Field</th>
                                <th class="py-2 font-semibold w-1/3">Old Value</th>
                                <th class="py-2 font-semibold w-1/3">New Value (Changes)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="border-color:var(--border)">
                            @foreach($log->properties['attributes'] as $field => $newValue)
                                @php
                                    $oldValue = $log->properties['old'][$field] ?? null;
                                @endphp
                                @if($oldValue !== $newValue)
                                    <tr style="color:var(--text)">
                                        <td class="py-3 font-semibold font-mono">{{ $field }}</td>
                                        <td class="py-3 pr-2 break-all text-red-600 font-mono" style="background:#fee2e2">{{ is_scalar($oldValue) ? $oldValue : json_encode($oldValue) }}</td>
                                        <td class="py-3 break-all text-emerald-700 font-mono" style="background:#dcfce7">{{ is_scalar($newValue) ? $newValue : json_encode($newValue) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @elseif(isset($log->properties['attributes']))
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b" style="border-color:var(--border); color:var(--text-muted)">
                                <th class="py-2 font-semibold w-1/3">Field</th>
                                <th class="py-2 font-semibold">Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="border-color:var(--border)">
                            @foreach($log->properties['attributes'] as $field => $value)
                                <tr style="color:var(--text)">
                                    <td class="py-2 font-semibold font-mono">{{ $field }}</td>
                                    <td class="py-2 break-all font-mono">{{ is_scalar($value) ? $value : json_encode($value) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-6 text-sm" style="color:var(--text-muted)">
                    No properties payloads recorded for this event.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
