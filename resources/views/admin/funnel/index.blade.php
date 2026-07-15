@extends('admin.layout')
@section('title', 'Funnel Analytics')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold" style="color:var(--text)">Funnel Analytics</h2>
        <select id="period-select" class="text-sm" style="padding:6px 12px;border:1px solid var(--border);border-radius:8px">
            <option value="week">Last 7 Days</option>
            <option value="month" selected>Last 30 Days</option>
            <option value="all">All Time</option>
        </select>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="stat-cards">
        <div class="card p-4 text-center"><div class="text-3xl font-bold" style="color:var(--primary)">—</div><div class="text-xs mt-1" style="color:var(--text-muted)">Sessions</div></div>
        <div class="card p-4 text-center"><div class="text-3xl font-bold" style="color:var(--primary)">—</div><div class="text-xs mt-1" style="color:var(--text-muted)">Leads</div></div>
        <div class="card p-4 text-center"><div class="text-3xl font-bold" style="color:var(--primary)">—</div><div class="text-xs mt-1" style="color:var(--text-muted)">Deals</div></div>
        <div class="card p-4 text-center"><div class="text-3xl font-bold" style="color:var(--primary)">—</div><div class="text-xs mt-1" style="color:var(--text-muted)">Win Rate</div></div>
    </div>

    <div class="card p-6">
        <h3 class="text-sm font-semibold mb-4" style="color:var(--text)">Daily Trend</h3>
        <canvas id="funnel-chart" height="250"></canvas>
    </div>

    <div class="grid grid-cols-3 gap-4 text-sm text-center" id="rate-cards">
        <div class="card p-3"><strong id="lead-rate">—</strong><br><span style="color:var(--text-muted)">Lead Conv. Rate</span></div>
        <div class="card p-3"><strong id="deal-rate">—</strong><br><span style="color:var(--text-muted)">Lead → Deal Rate</span></div>
        <div class="card p-3"><strong id="new-leads">—</strong><br><span style="color:var(--text-muted)">New Leads (24h)</span></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
function loadFunnelData(period) {
    fetch('{{ route("admin.funnel.data") }}?period=' + period)
        .then(r => r.json())
        .then(d => {
            document.querySelector('#stat-cards .card:nth-child(1) .text-3xl').textContent = d.sessions;
            document.querySelector('#stat-cards .card:nth-child(2) .text-3xl').textContent = d.leads;
            document.querySelector('#stat-cards .card:nth-child(3) .text-3xl').textContent = d.deals;
            document.querySelector('#stat-cards .card:nth-child(4) .text-3xl').textContent = d.deal_win_rate + '%';
            document.getElementById('lead-rate').textContent = d.lead_to_deal_rate + '%';
            document.getElementById('deal-rate').textContent = d.lead_to_deal_rate + '%';
            document.getElementById('new-leads').textContent = d.new_leads;

            if (window._funnelChart) window._funnelChart.destroy();
            window._funnelChart = new Chart(document.getElementById('funnel-chart'), {
                type: 'line',
                data: {
                    labels: d.daily_labels,
                    datasets: [
                        { label: 'Sessions', data: d.daily_sessions, borderColor: '#3b82f6', tension: 0.3, fill: false },
                        { label: 'Leads', data: d.daily_leads, borderColor: '#10b981', tension: 0.3, fill: false },
                        { label: 'Conversions', data: d.daily_conversions, borderColor: '#f59e0b', tension: 0.3, fill: false },
                    ]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
            });
        });
}
document.getElementById('period-select').addEventListener('change', function() { loadFunnelData(this.value); });
loadFunnelData('month');
</script>
@endsection
