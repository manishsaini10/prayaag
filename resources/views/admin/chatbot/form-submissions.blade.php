@extends('admin.layout')

@section('title', 'Pre-Chat Form Submissions')

@section('actions')
    <a href="{{ url('/admin/chatbot/form-fields') }}" class="btn-secondary inline-flex items-center gap-1.5">
        &larr; Back to Form Builder
    </a>
    <a href="{{ url('/admin/chatbot') }}" class="btn-secondary inline-flex items-center gap-1.5">
        &larr; Chatbot Settings
    </a>
@endsection

@section('content')
<div class="space-y-6">
    <div class="card p-0 overflow-hidden overflow-x-auto">
        @if($leads->isEmpty())
            <div class="text-center py-12 text-sm" style="color:var(--text-muted)">
                No form submissions yet.
            </div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:1px solid var(--border)">
                        <th class="text-left px-4 py-3 font-semibold whitespace-nowrap" style="color:var(--text-muted)">Date</th>
                        @foreach($fields as $field)
                            <th class="text-left px-4 py-3 font-semibold whitespace-nowrap" style="color:var(--text-muted)">
                                {{ $field->label }}
                                @if($field->is_required)
                                    <span style="color:#dc2626">*</span>
                                @endif
                            </th>
                        @endforeach
                        <th class="text-left px-4 py-3 font-semibold whitespace-nowrap" style="color:var(--text-muted)">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leads as $lead)
                        <tr style="border-bottom:1px solid var(--border)" class="hover:bg-surface-2/50 transition">
                            <td class="px-4 py-3 whitespace-nowrap" style="color:var(--text-muted)">
                                {{ $lead->created_at->format('M j, Y H:i') }}
                            </td>
                            @foreach($fields as $field)
                                <td class="px-4 py-3 whitespace-nowrap" style="color:var(--text)">
                                    {{ $lead->form_data[$field->field_key] ?? '—' }}
                                </td>
                            @endforeach
                            <td class="px-4 py-3">
                                <span class="badge {{ $lead->status === 'new' ? '' : ($lead->status === 'contacted' ? 'style=background:#dbeafe;color:#1e40af' : ($lead->status === 'qualified' ? 'style=background:#dcfce7;color:#166534' : 'style=background:#fef2f2;color:#991b1b')) }}">{{ ucfirst($lead->status) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($leads->hasPages())
                <div class="p-4 border-t" style="border-color:var(--border)">
                    {{ $leads->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
