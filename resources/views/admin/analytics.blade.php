@extends('admin.layout')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Header & Range Picker --}}
    <form action="{{ url('/admin/analytics') }}" method="GET" id="analytics-filter-form" class="card p-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold tracking-tight" style="color:var(--text)">Analytics Dashboard</h2>
                <p class="text-xs" style="color:var(--text-muted)">Pre-aggregated daily metrics and actionable insights.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <label for="range-select" class="text-xs font-semibold" style="color:var(--text-muted)">Range:</label>
                    <select name="range" id="range-select" class="text-sm rounded-lg border px-3 py-1.5 focus:outline-none" style="background:var(--card-bg); border-color:var(--border); color:var(--text)">
                        <option value="7days" {{ $range === '7days' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="30days" {{ $range === '30days' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="90days" {{ $range === '90days' ? 'selected' : '' }}>Last 90 Days</option>
                        <option value="custom" {{ $range === 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>

                <div id="custom-dates-div" class="{{ $range === 'custom' ? 'flex' : 'hidden' }} items-center gap-2">
                    <input type="date" name="from" value="{{ $from }}" class="text-sm rounded-lg border px-2 py-1" style="background:var(--card-bg); border-color:var(--border); color:var(--text)" />
                    <span class="text-xs" style="color:var(--text-muted)">to</span>
                    <input type="date" name="to" value="{{ $to }}" class="text-sm rounded-lg border px-2 py-1" style="background:var(--card-bg); border-color:var(--border); color:var(--text)" />
                    <button type="submit" class="btn btn-sm btn-primary py-1 px-3">Apply</button>
                </div>

                <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-sm flex items-center gap-1.5 px-3 py-1.5 border rounded-lg text-sm transition-colors" style="border-color:var(--border); background:var(--card-bg); color:var(--text); hover:background:var(--border)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v12m0 0l-3-3m3 3l3-3m-9 7h12"/></svg>
                    <span>Export CSV</span>
                </a>
            </div>
        </div>
    </form>

    {{-- Stats Cards Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
        <div class="card p-4 rounded-xl border flex flex-col justify-between" style="border-color:var(--border)">
            <span class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted)">Total page views</span>
            <h3 class="text-2xl font-bold mt-2" style="color:var(--text)">{{ number_format($summary['total_views']) }}</h3>
        </div>
        <div class="card p-4 rounded-xl border flex flex-col justify-between" style="border-color:var(--border)">
            <span class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted)">Unique Visitors</span>
            <h3 class="text-2xl font-bold mt-2" style="color:var(--text)">{{ number_format($summary['unique_visitors']) }}</h3>
        </div>
        <div class="card p-4 rounded-xl border flex flex-col justify-between" style="border-color:var(--border)">
            <span class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted)">Total Leads</span>
            <h3 class="text-2xl font-bold mt-2" style="color:var(--text)">{{ number_format($summary['total_leads']) }}</h3>
        </div>
        <div class="card p-4 rounded-xl border flex flex-col justify-between" style="border-color:var(--border)">
            <span class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted)">Chatbot Convs</span>
            <h3 class="text-2xl font-bold mt-2" style="color:var(--text)">{{ number_format($summary['total_chatbot_conversations']) }}</h3>
        </div>
        <div class="card p-4 rounded-xl border flex flex-col justify-between" style="border-color:var(--border)">
            <span class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted)">Avg Session</span>
            <h3 class="text-2xl font-bold mt-2" style="color:var(--text)">{{ $summary['avg_session_duration'] }}s</h3>
        </div>
        <div class="card p-4 rounded-xl border flex flex-col justify-between" style="border-color:var(--border)">
            <span class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted)">Bounce Rate</span>
            <h3 class="text-2xl font-bold mt-2" style="color:var(--text)">{{ $summary['bounce_rate'] }}%</h3>
        </div>
    </div>

    {{-- Main Trend Chart --}}
    <div class="card p-5 rounded-xl border" style="border-color:var(--border)">
        <h3 class="text-sm font-semibold mb-4" style="color:var(--text)">Traffic & Visitor Trends</h3>
        <div style="position: relative; height: 320px;">
            <canvas id="traffic-trend-chart"></canvas>
        </div>
    </div>

    {{-- Grid 2 Column for Funnel and Sources --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Lead Funnel Widget --}}
        <div class="card p-5 rounded-xl border" style="border-color:var(--border)">
            <h3 class="text-sm font-semibold mb-4" style="color:var(--text)">Lead Funnel</h3>
            <div class="space-y-4 py-2">
                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1" style="color:var(--text)">
                        <span>Unique Visitors</span>
                        <span>{{ number_format($funnel['visitors']) }}</span>
                    </div>
                    <div class="w-full h-3 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                        <div class="h-full bg-blue-500 rounded-full" style="width: 100%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1" style="color:var(--text)">
                        <span>Chatbot Conversations</span>
                        <span>{{ number_format($funnel['conversations']) }} <span class="text-[10px] text-muted-foreground font-normal">({{ $funnel['visitor_to_chatbot_pct'] }}% of visitors)</span></span>
                    </div>
                    <div class="w-full h-3 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                        <div class="h-full bg-indigo-500 rounded-full" style="width: {{ min(100, max(2, $funnel['visitor_to_chatbot_pct'])) }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1" style="color:var(--text)">
                        <span>Leads Generated</span>
                        <span>{{ number_format($funnel['leads']) }} <span class="text-[10px] text-muted-foreground font-normal">({{ $funnel['chatbot_to_lead_pct'] }}% of chat convs)</span></span>
                    </div>
                    <div class="w-full h-3 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ min(100, max(2, $funnel['chatbot_to_lead_pct'])) }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1" style="color:var(--text)">
                        <span>Closed / Won Admissions</span>
                        <span>{{ number_format($funnel['admissions']) }} <span class="text-[10px] text-muted-foreground font-normal">({{ $funnel['lead_to_admission_pct'] }}% of leads)</span></span>
                    </div>
                    <div class="w-full h-3 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                        <div class="h-full bg-yellow-500 rounded-full" style="width: {{ min(100, max(2, $funnel['lead_to_admission_pct'])) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Traffic Sources Widget --}}
        <div class="card p-5 rounded-xl border flex flex-col justify-between" style="border-color:var(--border)">
            <div>
                <h3 class="text-sm font-semibold mb-2" style="color:var(--text)">Traffic Sources</h3>
                <div style="position: relative; height: 180px;" class="mb-4">
                    @if (empty($sources['visits']))
                        <div class="w-full h-full flex items-center justify-center text-xs" style="color:var(--text-muted)">No traffic sources found in this range.</div>
                    @else
                        <canvas id="traffic-sources-chart"></canvas>
                    @endif
                </div>
            </div>
            
            <div class="border-t pt-3" style="border-color:var(--border)">
                <table class="w-full text-xs text-left">
                    <thead>
                        <tr style="color:var(--text-muted)">
                            <th class="pb-1">Source</th>
                            <th class="text-right pb-1">Visits</th>
                            <th class="text-right pb-1">Leads</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(array_slice($sources['raw'], 0, 4) as $raw)
                            <tr style="color:var(--text)">
                                <td class="py-1 font-medium">{{ $raw->source }}</td>
                                <td class="text-right py-1">{{ number_format($raw->visits) }}</td>
                                <td class="text-right py-1">{{ number_format($raw->leads) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-2" style="color:var(--text-muted)">No source data available.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Grid 2 Column for Pages and Chatbot --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top Pages Table --}}
        <div class="card p-5 rounded-xl border" style="border-color:var(--border)">
            <h3 class="text-sm font-semibold mb-4" style="color:var(--text)">Top Visited Pages</h3>
            <table class="w-full text-xs text-left">
                <thead>
                    <tr style="color:var(--text-muted)" class="border-b">
                        <th class="pb-2">Page URL</th>
                        <th class="text-right pb-2">Views</th>
                        <th class="text-right pb-2">Unique Views</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:var(--border)">
                    @forelse ($topPages as $page)
                        <tr style="color:var(--text)">
                            <td class="py-2.5 font-medium truncate max-w-xs">{{ $page->url }}</td>
                            <td class="text-right py-2.5">{{ number_format($page->views) }}</td>
                            <td class="text-right py-2.5">{{ number_format($page->unique_views) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center py-4" style="color:var(--text-muted)">No page views recorded in this range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Chatbot Performance Widget --}}
        <div class="card p-5 rounded-xl border flex flex-col justify-between" style="border-color:var(--border)">
            <div>
                <h3 class="text-sm font-semibold mb-4" style="color:var(--text)">Chatbot Insights</h3>
                <div class="grid grid-cols-3 gap-3 mb-6">
                    <div class="p-3 rounded-lg border text-center" style="background:var(--border); border-color:var(--border)">
                        <div class="text-lg font-bold" style="color:var(--text)">{{ $chatbot['ai_handled_percent'] }}%</div>
                        <div class="text-[10px]" style="color:var(--text-muted)">AI Handled</div>
                    </div>
                    <div class="p-3 rounded-lg border text-center" style="background:var(--border); border-color:var(--border)">
                        <div class="text-lg font-bold" style="color:var(--text)">{{ $chatbot['avg_rating'] }} ★</div>
                        <div class="text-[10px]" style="color:var(--text-muted)">User Rating</div>
                    </div>
                    <div class="p-3 rounded-lg border text-center" style="background:var(--border); border-color:var(--border)">
                        <div class="text-lg font-bold" style="color:var(--text)">{{ $chatbot['avg_response_time'] }}s</div>
                        <div class="text-[10px]" style="color:var(--text-muted)">Response Time</div>
                    </div>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:var(--text-muted)">Top User Intents</h4>
                    @forelse ($chatbot['top_intents'] as $intent)
                        <div class="flex items-center justify-between text-xs py-1" style="color:var(--text)">
                            <span class="capitalize font-medium">{{ str_replace('_', ' ', $intent->intent) }}</span>
                            <span class="font-bold">{{ number_format($intent->count) }} matches</span>
                        </div>
                    @empty
                        <div class="text-xs text-center py-2" style="color:var(--text-muted)">No intents tracked.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Popups & SEO broken links --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Popup Conversions --}}
        <div class="card p-5 rounded-xl border" style="border-color:var(--border)">
            <h3 class="text-sm font-semibold mb-4" style="color:var(--text)">Popup Conversions</h3>
            <table class="w-full text-xs text-left">
                <thead>
                    <tr style="color:var(--text-muted)" class="border-b">
                        <th class="pb-2">Popup Name</th>
                        <th class="text-right pb-2">Impressions</th>
                        <th class="text-right pb-2">Conversions</th>
                        <th class="text-right pb-2">Conversion Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm" style="border-color:var(--border)">
                    @forelse ($popups as $popup)
                        <tr style="color:var(--text)">
                            <td class="py-2.5 font-medium">{{ $popup['name'] }}</td>
                            <td class="text-right py-2.5">{{ number_format($popup['impressions']) }}</td>
                            <td class="text-right py-2.5">{{ number_format($popup['conversions']) }}</td>
                            <td class="text-right py-2.5 font-semibold text-emerald-500">{{ $popup['conversion_rate'] }}%</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-4 text-xs" style="color:var(--text-muted)">No popup impressions or conversions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- SEO 404 broken links --}}
        <div class="card p-5 rounded-xl border" style="border-color:var(--border)">
            <h3 class="text-sm font-semibold mb-4" style="color:var(--text)">Broken Links (404 Logs)</h3>
            <table class="w-full text-xs text-left">
                <thead>
                    <tr style="color:var(--text-muted)" class="border-b">
                        <th class="pb-2">URL Path</th>
                        <th class="text-right pb-2">Occurrences</th>
                        <th class="text-right pb-2">Last Seen</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:var(--border)">
                    @forelse ($notFound as $log)
                        <tr style="color:var(--text)">
                            <td class="py-2.5 font-medium truncate max-w-xs" style="color:var(--primary)">{{ $log->path }}</td>
                            <td class="text-right py-2.5 font-semibold">{{ number_format($log->count) }}</td>
                            <td class="text-right py-2.5" style="color:var(--text-muted)">{{ \Carbon\Carbon::parse($log->last_seen)->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center py-4" style="color:var(--text-muted)">Clean health! No 404 errors logged in this range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Toggle custom date range view
    const rangeSelect = document.getElementById('range-select');
    const customDates = document.getElementById('custom-dates-div');
    rangeSelect.addEventListener('change', function () {
        if (this.value === 'custom') {
            customDates.classList.remove('hidden');
            customDates.classList.add('flex');
        } else {
            customDates.classList.add('hidden');
            customDates.classList.remove('flex');
            this.form.submit();
        }
    });

    // 2. Render Traffic Trend Chart (Line)
    const trafficTrendCtx = document.getElementById('traffic-trend-chart').getContext('2d');
    new Chart(trafficTrendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($summary['chart']['labels']) !!},
            datasets: [
                {
                    label: 'Page Views',
                    data: {!! json_encode($summary['chart']['views']) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.05)',
                    tension: 0.35,
                    borderWidth: 2.5,
                    fill: true
                },
                {
                    label: 'Unique Visitors',
                    data: {!! json_encode($summary['chart']['visitors']) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.05)',
                    tension: 0.35,
                    borderWidth: 2.5,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 15, padding: 15 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(148, 163, 184, 0.1)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // 3. Render Traffic Sources Chart (Doughnut)
    const sourcesCanvas = document.getElementById('traffic-sources-chart');
    if (sourcesCanvas) {
        new Chart(sourcesCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($sources['labels']) !!},
                datasets: [{
                    data: {!! json_encode($sources['visits']) !!},
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#64748b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { boxWidth: 12, font: { size: 10 } }
                    }
                },
                cutout: '65%'
            }
        });
    }
});
</script>
@endsection
