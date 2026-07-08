@extends('admin.layout')

@section('title', 'Analytics')

@section('content')
    <div>
        <div class="stat"><b>{{ number_format($total) }}</b><span>Total page views</span></div>
        <div class="stat"><b>{{ number_format($last7) }}</b><span>Views, last 7 days</span></div>
    </div>

    <div class="card" style="margin-top: 8px;">
        <table>
            <thead>
                <tr><th>Page path</th><th>Views</th></tr>
            </thead>
            <tbody>
                @forelse ($topPages as $row)
                    <tr>
                        <td>/{{ ltrim($row->path, '/') }}</td>
                        <td>{{ number_format($row->views) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="empty">No views recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="muted" style="margin-top:12px;font-size:12px;">
        First-party data only — collected on public page loads. No third-party trackers.
    </p>
@endsection
