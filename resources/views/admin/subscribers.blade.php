@extends('admin.layout')

@section('title', 'Subscribers')

@section('content')
    <div class="card">
        <table>
            <thead>
                <tr><th>Subscribed</th><th>Email</th><th>Name</th><th>Source</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse ($subscribers as $subscriber)
                    <tr>
                        <td class="muted">{{ optional($subscriber->subscribed_at)->toDateString() ?? $subscriber->created_at?->toDateString() }}</td>
                        <td>{{ $subscriber->email }}</td>
                        <td>{{ $subscriber->name ?? '—' }}</td>
                        <td class="muted">{{ $subscriber->source ?? '—' }}</td>
                        <td><span class="badge {{ $subscriber->status }}">{{ $subscriber->status }}</span></td>
                        <td>
                            @if ($subscriber->status === 'subscribed')
                                <form method="POST" action="{{ url('/admin/subscribers/'.$subscriber->id.'/unsubscribe') }}" style="display:inline">
                                    @csrf
                                    <button class="btn-sm" type="submit">Unsubscribe</button>
                                </form>
                            @else
                                <span class="muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">No subscribers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
