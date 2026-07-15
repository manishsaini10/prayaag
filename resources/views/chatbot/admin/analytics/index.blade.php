@extends('admin.layout')
@section('title', 'Chatbot Analytics')
@section('actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.chatbot.analytics.reports') }}" class="btn-secondary inline-flex items-center gap-1.5">Reports</a>
        <a href="{{ route('admin.chatbot.index') }}" class="btn-secondary inline-flex items-center gap-1.5">&larr; Settings</a>
    </div>
@endsection
@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card p-4 text-center">
            <div class="text-2xl font-bold" style="color:var(--primary)">{{ $totalConversations }}</div>
            <div class="text-xs font-semibold mt-1" style="color:var(--text-muted)">Total Conversations</div>
        </div>
        <div class="card p-4 text-center">
            <div class="text-2xl font-bold" style="color:#16a34a">{{ $totalMessages }}</div>
            <div class="text-xs font-semibold mt-1" style="color:var(--text-muted)">Total Messages</div>
        </div>
        <div class="card p-4 text-center">
            <div class="text-2xl font-bold" style="color:#8b5cf6">{{ $activeAgents }}</div>
            <div class="text-xs font-semibold mt-1" style="color:var(--text-muted)">Active Agents</div>
        </div>
        <div class="card p-4 text-center">
            <div class="text-2xl font-bold" style="color:#a855f7">{{ $openTickets }}</div>
            <div class="text-xs font-semibold mt-1" style="color:var(--text-muted)">Open Tickets</div>
        </div>
    </div>

    <div class="card p-6">
        <h3 class="text-sm font-semibold mb-4" style="color:var(--text)">Conversation Trends (Last 30 Days)</h3>
        <canvas id="conversationChart" height="100"></canvas>
    </div>

    <div class="card p-6">
        <h3 class="text-sm font-semibold mb-4" style="color:var(--text)">Performance Summary</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:1px solid var(--border)">
                        <th class="text-left px-4 py-2 font-semibold" style="color:var(--text-muted)">Metric</th>
                        <th class="text-left px-4 py-2 font-semibold" style="color:var(--text-muted)">Today</th>
                        <th class="text-left px-4 py-2 font-semibold" style="color:var(--text-muted)">This Week</th>
                        <th class="text-left px-4 py-2 font-semibold" style="color:var(--text-muted)">This Month</th>
                        <th class="text-left px-4 py-2 font-semibold" style="color:var(--text-muted)">All Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($metrics as $metric)
                        <tr style="border-bottom:1px solid var(--border)">
                            <td class="px-4 py-2 font-medium" style="color:var(--text)">{{ $metric['label'] }}</td>
                            <td class="px-4 py-2" style="color:var(--text)">{{ $metric['today'] }}</td>
                            <td class="px-4 py-2" style="color:var(--text)">{{ $metric['week'] }}</td>
                            <td class="px-4 py-2" style="color:var(--text)">{{ $metric['month'] }}</td>
                            <td class="px-4 py-2" style="color:var(--text)">{{ $metric['all'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('conversationChart');
    if (!ctx) return;
    fetch('{{ route("admin.chatbot.analytics.realtime") }}')
        .then(r => r.json())
        .then(data => {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels || [],
                    datasets: [{
                        label: 'Conversations',
                        data: data.values || [],
                        borderColor: '#0b2545',
                        backgroundColor: 'rgba(11,37,69,0.1)',
                        fill: true,
                        tension: 0.4,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        });
});
</script>
@endpush
