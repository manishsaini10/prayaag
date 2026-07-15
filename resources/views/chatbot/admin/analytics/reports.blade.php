@extends('admin.layout')
@section('title', 'Analytics Reports')
@section('actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.chatbot.analytics.index') }}" class="btn-secondary inline-flex items-center gap-1.5">&larr; Dashboard</a>
    </div>
@endsection
@section('content')
<div class="space-y-6">
    <div class="card p-6">
        <h3 class="text-sm font-semibold mb-4" style="color:var(--text)">Generate Report</h3>
        <form method="POST" action="{{ route('admin.chatbot.analytics.reports.generate') }}" class="flex items-end gap-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Report Type</label>
                <select name="type" class="w-full">
                    <option value="conversations">Conversations</option>
                    <option value="messages">Messages</option>
                    <option value="leads">Leads</option>
                    <option value="tickets">Tickets</option>
                    <option value="agent">Agent Performance</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Date From</label>
                <input type="date" name="date_from" class="w-full">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Date To</label>
                <input type="date" name="date_to" class="w-full">
            </div>
            <button type="submit" class="btn-primary">Generate</button>
        </form>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="px-4 py-3 font-semibold text-sm border-b" style="border-color:var(--border);color:var(--text)">Generated Reports</div>
        @if($reports->isEmpty())
            <div class="text-center py-8 text-sm" style="color:var(--text-muted)">No reports generated yet.</div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:1px solid var(--border)">
                        <th class="text-left px-4 py-2 font-semibold" style="color:var(--text-muted)">Type</th>
                        <th class="text-left px-4 py-2 font-semibold" style="color:var(--text-muted)">Date Range</th>
                        <th class="text-left px-4 py-2 font-semibold" style="color:var(--text-muted)">Generated At</th>
                        <th class="text-left px-4 py-2 font-semibold" style="color:var(--text-muted)">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                        <tr style="border-bottom:1px solid var(--border)" class="hover:bg-surface-2/50">
                            <td class="px-4 py-2" style="color:var(--text)">{{ $report->type }}</td>
                            <td class="px-4 py-2" style="color:var(--text-muted)">{{ $report->date_from ? $report->date_from->format('M j, Y') : '—' }} - {{ $report->date_to ? $report->date_to->format('M j, Y') : '—' }}</td>
                            <td class="px-4 py-2" style="color:var(--text-muted)">{{ $report->created_at->format('M j, Y H:i') }}</td>
                            <td class="px-4 py-2">
                                <form method="POST" action="{{ route('admin.chatbot.analytics.reports.generate') }}" style="display:inline">
                                    @csrf
                                    <input type="hidden" name="type" value="{{ $report->type }}">
                                    <input type="hidden" name="date_from" value="{{ $report->date_from?->format('Y-m-d') }}">
                                    <input type="hidden" name="date_to" value="{{ $report->date_to?->format('Y-m-d') }}">
                                    <button type="submit" class="text-sm font-medium hover:underline" style="color:var(--primary)">Regenerate</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
