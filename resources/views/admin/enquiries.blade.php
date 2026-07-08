@extends('admin.layout')

@section('title', 'Enquiries')

@section('content')
    <div class="card">
        <table>
            <thead>
                <tr><th>Received</th><th>Type</th><th>Name</th><th>Email</th><th>Message</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse ($enquiries as $enquiry)
                    <tr>
                        <td class="muted">{{ $enquiry->created_at?->diffForHumans() }}</td>
                        <td>{{ $enquiry->type }}</td>
                        <td>{{ $enquiry->name }}</td>
                        <td><a class="link" href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a></td>
                        <td>{{ \Illuminate\Support\Str::limit($enquiry->message, 120) }}</td>
                        <td><span class="badge {{ $enquiry->status }}">{{ $enquiry->status }}</span></td>
                        <td>
                            @if ($enquiry->status !== 'read')
                                <form method="POST" action="{{ url('/admin/enquiries/'.$enquiry->id.'/status') }}" style="display:inline">
                                    @csrf
                                    <input type="hidden" name="status" value="read">
                                    <button class="btn-sm" type="submit">Mark read</button>
                                </form>
                            @endif
                            @if ($enquiry->status !== 'archived')
                                <form method="POST" action="{{ url('/admin/enquiries/'.$enquiry->id.'/status') }}" style="display:inline">
                                    @csrf
                                    <input type="hidden" name="status" value="archived">
                                    <button class="btn-sm" type="submit">Archive</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">No enquiries yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
