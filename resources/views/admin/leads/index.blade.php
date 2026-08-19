@extends('admin.layout')

@section('title', 'Admission Leads')
@section('subtitle', $stats['total'] . ' total · ' . $stats['new'] . ' new · ' . $stats['read'] . ' contacted')

@section('actions')
    <div style="display:flex;gap:8px;align-items:center">
        <a href="{{ route('admin.leads.export.csv', request()->query()) }}" class="btn">
            <x-admin.icon name="download"/> Export CSV
        </a>
        <a href="{{ route('admin.leads.export.pdf', request()->query()) }}" target="_blank" class="btn primary">
            <x-admin.icon name="printer"/> Export PDF / Print
        </a>
    </div>
@endsection

@section('content')

@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px;font-weight:500">{{ session('status') }}</div>
@endif

<!-- Filters & Search Bar -->
<div class="card mb-4" style="padding:14px 18px;margin-bottom:16px">
    <form method="GET" action="{{ route('admin.leads') }}" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
        <div style="flex:1;min-width:200px">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search parent name, student, email, phone..." style="width:100%;padding:8px 12px;border:1px solid var(--border-strong);border-radius:8px;background:var(--surface);color:var(--text);font:inherit;font-size:13.5px">
        </div>
        <div>
            <select name="status" onchange="this.form.submit()" style="padding:8px 12px;border:1px solid var(--border-strong);border-radius:8px;background:var(--surface);color:var(--text);font:inherit;font-size:13.5px">
                <option value="">All Statuses</option>
                <option value="new" @selected(request('status') === 'new')>New ({{ $stats['new'] }})</option>
                <option value="read" @selected(request('status') === 'read')>Contacted ({{ $stats['read'] }})</option>
                <option value="archived" @selected(request('status') === 'archived')>Archived ({{ $stats['archived'] }})</option>
            </select>
        </div>
        @if (request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.leads') }}" class="btn-sm" style="height:36px;display:inline-flex;align-items:center">Reset</a>
        @endif
        <button type="submit" class="btn-sm primary" style="height:36px">Filter</button>
    </form>
</div>

<div class="card" style="overflow:hidden">
    <table>
        <thead>
            <tr>
                <th>Received</th>
                <th>Parent & Contact</th>
                <th>Student Details</th>
                <th>Class Applying</th>
                <th>Address</th>
                <th>Message / Query</th>
                <th>Status</th>
                <th style="text-align:right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($leads as $lead)
                @php $meta = $lead->meta ?? []; @endphp
                <tr>
                    <td style="white-space:nowrap">
                        <div style="font-weight:600;color:var(--text);font-size:12.5px">{{ $lead->created_at?->format('d M, Y') }}</div>
                        <div class="muted" style="font-size:11.5px">{{ $lead->created_at?->format('h:i A') }} ({{ $lead->created_at?->diffForHumans() }})</div>
                    </td>
                    <td>
                        <strong>{{ $lead->name }}</strong>
                        @if (!empty($meta['father_name']))
                            <div style="font-size:12px;color:var(--text-muted)">👨 <strong>Father:</strong> {{ $meta['father_name'] }}</div>
                        @endif
                        @if (!empty($meta['mother_name']))
                            <div style="font-size:12px;color:var(--text-muted)">👩 <strong>Mother:</strong> {{ $meta['mother_name'] }}</div>
                        @endif
                        <div><a class="link" href="mailto:{{ $lead->email }}">{{ $lead->email }}</a></div>
                        @if ($lead->phone)<div class="muted" style="font-size:12px">📞 {{ $lead->phone }}</div>@endif
                    </td>
                    <td>
                        <strong style="color:var(--primary)">{{ $meta['student_name'] ?? '—' }}</strong>
                        @if (!empty($meta['dob']))
                            <div style="font-size:12px;color:var(--text);font-weight:600;margin-top:2px">
                                🎂 <strong>DOB:</strong> {{ \Carbon\Carbon::parse($meta['dob'])->format('d M, Y') }}
                            </div>
                        @endif
                        @if (!empty($meta['gender']))
                            <div class="muted" style="font-size:12px;text-transform:capitalize">Gender: {{ $meta['gender'] }}</div>
                        @endif
                        @if (!empty($meta['previous_school']))
                            <div class="muted" style="font-size:11px">🏫 {{ $meta['previous_school'] }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="badge" style="background:var(--primary-soft);color:var(--primary);font-weight:600;padding:4px 8px">
                            {{ $meta['class_applying'] ?? '—' }}
                        </span>
                    </td>
                    <td>
                        @if (!empty($meta['address']))
                            <div style="font-size:12.5px;color:var(--text);max-width:200px">📍 {{ $meta['address'] }}</div>
                        @else
                            <span class="muted" style="font-size:12px">—</span>
                        @endif
                    </td>
                    <td>
                        @if ($lead->subject)<div style="font-weight:600;font-size:12.5px">{{ $lead->subject }}</div>@endif
                        <div class="muted" style="font-size:12px">{{ \Illuminate\Support\Str::limit($lead->message, 90) }}</div>
                    </td>
                    <td><span class="badge {{ $lead->status }}">{{ $lead->status }}</span></td>
                    <td style="text-align:right;white-space:nowrap">
                        @if ($lead->status !== 'read')
                            <form method="POST" action="{{ url('/admin/enquiries/'.$lead->id.'/status') }}" style="display:inline">
                                @csrf <input type="hidden" name="status" value="read">
                                <button class="btn-sm" type="submit">Mark contacted</button>
                            </form>
                        @endif
                        @if ($lead->status !== 'archived')
                            <form method="POST" action="{{ url('/admin/enquiries/'.$lead->id.'/status') }}" style="display:inline">
                                @csrf <input type="hidden" name="status" value="archived">
                                <button class="btn-sm" type="submit">Archive</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="empty">No admission leads match your filter criteria.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($leads->hasPages())
    <div class="flex items-center justify-between mt-4 text-[13px]" style="color:var(--text-muted)">
        <span>Page {{ $leads->currentPage() }} of {{ $leads->lastPage() }}</span>
        <div class="flex items-center gap-2">
            @if ($leads->onFirstPage())<span class="btn-sm" style="opacity:.5">Previous</span>@else<a class="btn-sm" href="{{ $leads->previousPageUrl() }}">Previous</a>@endif
            @if ($leads->hasMorePages())<a class="btn-sm" href="{{ $leads->nextPageUrl() }}">Next</a>@else<span class="btn-sm" style="opacity:.5">Next</span>@endif
        </div>
    </div>
@endif

@endsection
