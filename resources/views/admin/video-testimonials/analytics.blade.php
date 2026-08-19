@extends('admin.layout')

@section('title', 'Video Testimonials — Analytics')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.video-testimonials.index') }}" class="text-sm font-medium" style="color:var(--text-muted)">← Video Testimonials</a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="stat-value text-blue-600">{{ $totals['total_videos'] }}</div>
            <div class="stat-label">Published Videos</div>
        </div>
        <div class="stat-card">
            <div class="stat-value text-emerald-600">{{ number_format($totals['total_views']) }}</div>
            <div class="stat-label">Total Views</div>
        </div>
        <div class="stat-card">
            <div class="stat-value text-purple-600">{{ $totals['avg_watch'] }}%</div>
            <div class="stat-label">Avg Watch %</div>
        </div>
        <div class="stat-card">
            <div class="stat-value text-amber-600">{{ number_format($totals['cta_clicks']) }}</div>
            <div class="stat-label">CTA Clicks</div>
        </div>
    </div>

    {{-- Analytics Table --}}
    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:var(--border)">
            <h2 class="font-bold text-sm" style="color:var(--text)">Per-Video Performance</h2>
            <p class="text-xs" style="color:var(--text-muted)">Click column headers to sort</p>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b" style="background:var(--surface-raised)">
                    <th class="px-4 py-3 text-left font-semibold text-xs uppercase tracking-wide" style="color:var(--text-muted)">Video</th>
                    @foreach([
                        'views_count' => 'Views',
                        'avg_watch'   => 'Avg Watch %',
                        'cta_clicks'  => 'CTA Clicks',
                    ] as $col => $label)
                    <th class="px-4 py-3 text-center font-semibold text-xs uppercase tracking-wide" style="color:var(--text-muted)">
                        <a href="{{ route('admin.video-testimonials.analytics', ['sort' => $col, 'dir' => ($sortBy === $col && $sortDir === 'desc') ? 'asc' : 'desc']) }}"
                           class="inline-flex items-center gap-1 hover:underline">
                            {{ $label }}
                            @if($sortBy === $col)
                            <span>{{ $sortDir === 'desc' ? '↓' : '↑' }}</span>
                            @endif
                        </a>
                    </th>
                    @endforeach
                    <th class="px-4 py-3 text-center font-semibold text-xs uppercase tracking-wide" style="color:var(--text-muted)">Completion</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="divide-color:var(--border)">
                @forelse($videos as $video)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if($video->thumbnail_url)
                            <img src="{{ $video->thumbnail_url }}" alt="" class="w-14 h-9 rounded object-cover shrink-0" loading="lazy">
                            @endif
                            <div>
                                <p class="font-semibold text-sm" style="color:var(--text)">{{ \Illuminate\Support\Str::limit($video->title, 50) }}</p>
                                <p class="text-xs" style="color:var(--text-muted)">{{ $video->student_name ?? '—' }} {{ $video->class_grade ? "· {$video->class_grade}" : '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="font-bold text-sm" style="color:var(--text)">{{ number_format($video->total_views) }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="font-bold text-sm {{ $video->avg_watch >= 50 ? 'text-emerald-600' : ($video->avg_watch >= 25 ? 'text-amber-600' : 'text-rose-600') }}">
                            {{ round($video->avg_watch, 1) }}%
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="font-bold text-sm text-amber-600">{{ number_format($video->cta_clicks) }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-2 rounded-full overflow-hidden" style="background:#f1f5f9">
                                <div class="h-full rounded-full transition-all"
                                     style="width:{{ min(100, round($video->avg_watch)) }}%;background:{{ $video->avg_watch >= 50 ? '#16a34a' : ($video->avg_watch >= 25 ? '#d97706' : '#dc2626') }}">
                                </div>
                            </div>
                            <span class="text-xs font-semibold w-8 text-right" style="color:var(--text-muted)">{{ round($video->avg_watch) }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-12 text-center" style="color:var(--text-muted)">
                        <div class="text-4xl mb-3">📊</div>
                        <p class="font-medium">No analytics data yet</p>
                        <p class="text-xs mt-1">Views are tracked when visitors watch videos on the public site.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
