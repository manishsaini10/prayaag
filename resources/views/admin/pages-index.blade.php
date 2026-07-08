@extends('admin.layout')

@section('title', 'Pages')
@section('subtitle', $pages->count() . ' ' . \Illuminate\Support\Str::plural('page', $pages->count()))

@section('content')

@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px;font-weight:500">
        {{ session('status') }}
    </div>
@endif

<div class="card" style="overflow:hidden">
    <table>
        <thead>
            <tr><th>Title</th><th>Slug</th><th>Status</th><th style="text-align:right">Actions</th></tr>
        </thead>
        <tbody>
            @forelse ($pages as $page)
                <tr>
                    <td><strong>{{ $page->title }}</strong></td>
                    <td><span class="muted">/{{ $page->slug }}</span></td>
                    <td><span class="badge {{ $page->status }}">{{ $page->status }}</span></td>
                    <td style="text-align:right;white-space:nowrap">
                        <a class="btn-sm" href="{{ url('/'.ltrim($page->slug, '/')) }}" target="_blank" rel="noopener">View</a>
                        <a class="btn-sm primary" href="{{ url('/admin/pages/'.$page->id.'/edit') }}">Edit</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty">No pages yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
