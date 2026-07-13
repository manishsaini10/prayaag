@extends('admin.layout')

@section('title', 'Captured Chatbot Leads')

@section('actions')
    <a href="{{ url('/admin/chatbot') }}" class="btn-secondary inline-flex items-center gap-1.5">
        &larr; Back to Settings
    </a>
@endsection

@section('content')
<div class="space-y-6">
    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:1px solid var(--border)">
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Lead Name</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Email</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Phone</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Class Interest</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Source</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Captured At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                        <tr style="border-bottom:1px solid var(--border)" class="hover:bg-surface-2/50 transition">
                            <td class="px-4 py-3 font-semibold" style="color:var(--text)">
                                {{ $lead->name }}
                            </td>
                            <td class="px-4 py-3" style="color:var(--text)">
                                {{ $lead->email ?? '—' }}
                            </td>
                            <td class="px-4 py-3" style="color:var(--text)">
                                {{ $lead->phone ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge">{{ $lead->admission_class ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3" style="color:var(--text-muted)">
                                {{ strtoupper($lead->source) }}
                            </td>
                            <td class="px-4 py-3" style="color:var(--text-muted)">
                                {{ $lead->created_at->format('M j, Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-sm" style="color:var(--text-muted)">
                                No leads captured by the chatbot yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leads->hasPages())
            <div class="p-4 border-t" style="border-color:var(--border)">
                {{ $leads->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
