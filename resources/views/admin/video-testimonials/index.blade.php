@extends('admin.layout')

@section('title', 'Video Testimonials')

@section('actions')
    <a href="{{ route('admin.video-testimonials.create') }}" class="btn-primary inline-flex items-center gap-1.5">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M12 5v14M5 12h14"/></svg>
        <span>Add Video</span>
    </a>
    <a href="{{ route('admin.video-testimonials.settings') }}" class="btn-secondary inline-flex items-center gap-1.5">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
        <span>Settings</span>
    </a>
    <a href="{{ route('admin.video-testimonials.analytics') }}" class="btn-secondary inline-flex items-center gap-1.5">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M3 18l4-8 4 4 4-6 4 8"/></svg>
        <span>Analytics</span>
    </a>
@endsection

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="px-4 py-3 rounded-xl text-sm" style="background:#dcfce7;color:#166534">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="px-4 py-3 rounded-xl text-sm" style="background:#fee2e2;color:#991b1b">{{ session('error') }}</div>
    @endif

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
        @foreach([
            ['Total', $stats['total'], ''],
            ['Pending', $stats['pending'], 'text-amber-500'],
            ['Approved', $stats['approved'], 'text-emerald-600'],
            ['Rejected', $stats['rejected'], 'text-rose-600'],
            ['Archived', $stats['archived'], 'text-slate-400'],
            ['Featured', $stats['featured'], 'text-purple-600'],
        ] as [$label, $count, $cls])
        <div class="stat-card">
            <div class="stat-value {{ $cls }}">{{ $count }}</div>
            <div class="stat-label">{{ $label }}</div>
        </div>
        @endforeach
    </div>

    {{-- Controls Bar --}}
    <div class="flex flex-col md:flex-row gap-4 items-center justify-between card p-4">
        <div class="flex flex-wrap gap-1 bg-gray-100 p-1 rounded-lg">
            @foreach([
                'pending'  => ['Pending',  $stats['pending']],
                'approved' => ['Approved', $stats['approved']],
                'rejected' => ['Rejected', $stats['rejected']],
                'archived' => ['Archived', $stats['archived']],
                'all'      => ['All',      $stats['total']],
            ] as $key => [$lbl, $count])
            @php
                $isActive = ($status === $key);
                $isPendingAlert = ($key === 'pending' && $stats['pending'] > 0);
            @endphp
            <a href="{{ route('admin.video-testimonials.index', ['status' => $key, 'q' => $search]) }}"
               class="relative px-3 py-1.5 text-xs font-semibold rounded-md transition-all flex items-center gap-1.5
               {{ $isActive ? 'bg-white shadow-sm text-gray-800' : 'text-gray-500 hover:text-gray-800' }}
               {{ $isPendingAlert && !$isActive ? 'border border-amber-300 bg-amber-50/50 text-amber-800' : '' }}">
                {{ $lbl }}
                <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full
                    {{ $isActive ? 'bg-gray-100 text-gray-700' : ($isPendingAlert ? 'bg-amber-500 text-white animate-pulse' : 'bg-gray-200/70 text-gray-500') }}">
                    {{ $count }}
                </span>
                @if($isPendingAlert && !$isActive)
                <span class="absolute -top-0.5 -right-0.5 flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                </span>
                @endif
            </a>
            @endforeach
        </div>

        <form action="{{ route('admin.video-testimonials.index') }}" method="GET" class="flex gap-2 w-full md:w-auto">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="text" name="q" value="{{ $search }}" placeholder="Search title, student, email..." class="text-sm px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 w-full md:w-64" style="focus:ring-color:var(--primary)">
            <button type="submit" class="btn-primary py-2 px-4 text-xs font-semibold">Search</button>
        </form>
    </div>

    {{-- Table --}}
    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b" style="background:var(--surface-raised)">
                    <th class="px-4 py-3 text-left font-semibold text-xs uppercase tracking-wide" style="color:var(--text-muted)">Video</th>
                    <th class="px-4 py-3 text-left font-semibold text-xs uppercase tracking-wide" style="color:var(--text-muted)">Student / Grade</th>
                    <th class="px-4 py-3 text-left font-semibold text-xs uppercase tracking-wide" style="color:var(--text-muted)">Provider</th>
                    <th class="px-4 py-3 text-left font-semibold text-xs uppercase tracking-wide" style="color:var(--text-muted)">Consent</th>
                    <th class="px-4 py-3 text-left font-semibold text-xs uppercase tracking-wide" style="color:var(--text-muted)">Status</th>
                    <th class="px-4 py-3 text-left font-semibold text-xs uppercase tracking-wide" style="color:var(--text-muted)">Views</th>
                    <th class="px-4 py-3 text-right font-semibold text-xs uppercase tracking-wide" style="color:var(--text-muted)">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="divide-color:var(--border)">
                @forelse($videos as $video)
                <tr class="hover:bg-gray-50/50 transition-colors" x-data="{ rejectOpen: false }">
                    {{-- Thumbnail + Title --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="relative shrink-0 w-16 h-10 rounded-lg overflow-hidden bg-gray-100">
                                @if($video->thumbnail_url)
                                <img src="{{ $video->thumbnail_url }}" alt="" class="w-full h-full object-cover" loading="lazy">
                                @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-sm truncate max-w-[200px]" style="color:var(--text)">{{ $video->title }}</p>
                                <p class="text-xs mt-0.5" style="color:var(--text-muted)">{{ $video->created_at->diffForHumans() }}</p>
                                @if($video->tags->isNotEmpty())
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach($video->tags->take(2) as $tag)
                                    <span class="px-1.5 py-0.5 text-[10px] font-semibold rounded-full" style="background:#e8eef8;color:#0e2f5e">{{ $tag->tag_value }}</span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                    </td>
                    {{-- Student --}}
                    <td class="px-4 py-3">
                        <p class="font-medium text-sm" style="color:var(--text)">{{ $video->student_name ?? '—' }}</p>
                        @if($video->class_grade)<span class="px-2 py-0.5 text-[10px] font-bold rounded-full" style="background:#e8eef8;color:#0e2f5e">{{ $video->class_grade }}</span>@endif
                    </td>
                    {{-- Provider --}}
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold capitalize" style="background:#f1f5f9;color:#475569">
                            {{ str_replace('_', ' ', $video->video_provider) }}
                        </span>
                    </td>
                    {{-- Consent --}}
                    <td class="px-4 py-3">
                        @if($video->consent_confirmed)
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold" style="background:#dcfce7;color:#166534">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="10" height="10"><path d="M20 6L9 17l-5-5"/></svg>
                            Consented
                        </span>
                        <p class="text-[10px] mt-0.5" style="color:var(--text-muted)">{{ $video->consent_signed_by }}</p>
                        @else
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold" style="background:#fee2e2;color:#991b1b">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="10" height="10"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            No Consent
                        </span>
                        @endif
                    </td>
                    {{-- Status --}}
                    <td class="px-4 py-3">
                        @php
                            $statusStyles = [
                                'pending'  => 'background:#fef3c7;color:#92400e',
                                'approved' => 'background:#dcfce7;color:#166534',
                                'rejected' => 'background:#fee2e2;color:#991b1b',
                                'archived' => 'background:#f1f5f9;color:#475569',
                            ];
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-semibold capitalize" style="{{ $statusStyles[$video->status] ?? '' }}">
                            {{ $video->status }}
                        </span>
                        @if($video->is_featured)
                        <span class="ml-1 text-xs">⭐</span>
                        @endif
                    </td>
                    {{-- Views --}}
                    <td class="px-4 py-3 text-sm" style="color:var(--text-muted)">{{ number_format($video->views_count) }}</td>
                    {{-- Actions --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1 flex-wrap">
                            {{-- Preview --}}
                            @if($video->video_embed_url)
                            <a href="{{ $video->video_embed_url }}" target="_blank" rel="noopener"
                               class="btn-secondary py-1 px-2 text-xs" title="Preview video">▶</a>
                            @endif

                            {{-- Approve --}}
                            @if($video->status !== 'approved')
                            @if($video->consent_confirmed)
                            <form method="POST" action="{{ route('admin.video-testimonials.approve', $video->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="btn-primary py-1 px-2 text-xs"
                                        onclick="return confirm('Approve this video testimonial and make it publicly visible?')">
                                    Approve
                                </button>
                            </form>
                            @else
                            <span class="px-2 py-1 text-xs rounded" style="background:#fee2e2;color:#991b1b" title="Cannot approve without consent">No Consent</span>
                            @endif
                            @endif

                            {{-- Reject --}}
                            @if($video->status !== 'rejected')
                            <button type="button" @click="rejectOpen = true" class="btn-secondary py-1 px-2 text-xs" style="color:#991b1b">
                                Reject
                            </button>
                            @endif

                            {{-- Edit --}}
                            <a href="{{ route('admin.video-testimonials.edit', $video->id) }}" class="btn-secondary py-1 px-2 text-xs">Edit</a>

                            {{-- Delete --}}
                            <form method="POST" action="{{ route('admin.video-testimonials.destroy', $video->id) }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-secondary py-1 px-2 text-xs" style="color:#991b1b"
                                        onclick="return confirm('Delete this video testimonial? This will also remove it from the video provider.')">🗑</button>
                            </form>
                        </div>

                        {{-- Reject Modal --}}
                        <div x-show="rejectOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                            <div class="absolute inset-0 bg-black/50" @click="rejectOpen = false"></div>
                            <div class="relative bg-white rounded-xl p-6 max-w-md w-full shadow-2xl">
                                <h3 class="font-bold text-lg mb-3">Reject Video Testimonial</h3>
                                <p class="text-sm text-gray-500 mb-4">Provide a reason (required):</p>
                                <form method="POST" action="{{ route('admin.video-testimonials.reject', $video->id) }}">
                                    @csrf
                                    <textarea name="rejection_reason" rows="3" required minlength="5"
                                              class="w-full border rounded-lg p-2 text-sm focus:outline-none focus:ring-2 mb-4"
                                              placeholder="e.g. Poor video quality, consent paperwork pending..."></textarea>
                                    <div class="flex gap-2 justify-end">
                                        <button type="button" @click="rejectOpen = false" class="btn-secondary py-2 px-4 text-sm">Cancel</button>
                                        <button type="submit" class="btn-primary py-2 px-4 text-sm" style="background:#dc2626">Reject</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center" style="color:var(--text-muted)">
                        <div class="text-4xl mb-3">🎬</div>
                        <p class="font-medium">No video testimonials found</p>
                        <p class="text-xs mt-1">{{ $status === 'pending' ? 'No videos awaiting review.' : 'Try a different filter.' }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $videos->links() }}
    </div>
</div>
@endsection
