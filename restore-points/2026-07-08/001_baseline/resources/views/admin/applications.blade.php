@extends('admin.layout')

@section('title', 'Job Applications')

@section('content')
    <div class="card">
        <table>
            <thead>
                <tr><th>Received</th><th>Applicant</th><th>Email</th><th>Position</th><th>Résumé</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse ($applications as $application)
                    <tr>
                        <td class="muted">{{ $application->created_at?->diffForHumans() }}</td>
                        <td>{{ $application->name }}</td>
                        <td><a class="link" href="mailto:{{ $application->email }}">{{ $application->email }}</a></td>
                        <td>{{ $application->jobListing?->title ?? '—' }}</td>
                        <td>
                            @if ($application->resume)
                                <a class="link" href="{{ url('/admin/applications/'.$application->id.'/resume') }}">Download</a>
                            @else
                                <span class="muted">—</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ url('/admin/applications/'.$application->id.'/status') }}" style="display:flex; gap:4px; align-items:center;">
                                @csrf
                                <select name="status" class="inline-select">
                                    @foreach (['new', 'reviewing', 'rejected', 'hired'] as $status)
                                        <option value="{{ $status }}" @selected($application->status === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                                <button class="btn-sm primary" type="submit">Set</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">No applications yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
