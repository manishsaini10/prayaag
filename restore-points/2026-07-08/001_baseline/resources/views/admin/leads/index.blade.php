@extends('admin.layout')

@section('title', 'Admission Leads')
@section('subtitle', $stats['total'] . ' total · ' . $stats['new'] . ' new')

@section('content')

@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px;font-weight:500">{{ session('status') }}</div>
@endif

<div class="card" style="overflow:hidden">
    <table>
        <thead>
            <tr><th>Received</th><th>Name</th><th>Contact</th><th>Message</th><th>Status</th><th style="text-align:right">Actions</th></tr>
        </thead>
        <tbody>
            @forelse ($leads as $lead)
                <tr>
                    <td class="muted">{{ $lead->created_at?->diffForHumans() }}</td>
                    <td><strong>{{ $lead->name }}</strong>@if ($lead->subject)<div class="muted" style="font-size:12px">{{ $lead->subject }}</div>@endif</td>
                    <td>
                        <a class="link" href="mailto:{{ $lead->email }}">{{ $lead->email }}</a>
                        @if ($lead->phone)<div class="muted" style="font-size:12px">{{ $lead->phone }}</div>@endif
                    </td>
                    <td>{{ \Illuminate\Support\Str::limit($lead->message, 110) }}</td>
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
                <tr><td colspan="6" class="empty">No admission leads yet. Enquiries submitted with type “admission” appear here.</td></tr>
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
