@extends('admin.layout')

@section('title', 'Privacy requests (GDPR)')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold tracking-tight" style="color:var(--text)">GDPR & Data Privacy Requests</h2>
            <p class="text-xs" style="color:var(--text-muted)">Manage personal data subject access requests, exports, and anonymizations.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="px-4 py-3 rounded-xl text-sm" style="background:#dcfce7;color:#166534">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="px-4 py-3 rounded-xl text-sm" style="background:#fee2e2;color:#991b1b">
            {{ session('error') }}
        </div>
    @endif

    <div class="card p-6 border rounded-xl overflow-hidden" style="border-color:var(--border)">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b" style="border-color:var(--border); color:var(--text-muted)">
                        <th class="py-3 font-semibold">Email</th>
                        <th class="py-3 font-semibold">Type</th>
                        <th class="py-3 font-semibold">Status</th>
                        <th class="py-3 font-semibold">IP Address</th>
                        <th class="py-3 font-semibold">Requested At</th>
                        <th class="py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:var(--border)">
                    @forelse($requests as $req)
                        <tr style="color:var(--text)">
                            <td class="py-4 font-medium">{{ $req->email }}</td>
                            <td class="py-4">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold uppercase" style="background:var(--surface-2)">
                                    {{ $req->request_type }}
                                </span>
                            </td>
                            <td class="py-4">
                                @if($req->status === 'completed')
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold" style="background:#dcfce7;color:#166534">Completed</span>
                                @elseif($req->status === 'verified')
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold" style="background:#dbeafe;color:#1e40af">Email Verified</span>
                                @elseif($req->status === 'rejected')
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold" style="background:#fee2e2;color:#991b1b">Rejected</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold" style="background:var(--surface-2);color:var(--text-muted)">{{ ucfirst($req->status) }}</span>
                                @endif
                            </td>
                            <td class="py-4 font-mono text-xs">{{ $req->ip_address }}</td>
                            <td class="py-4 text-xs">{{ $req->created_at->format('M j, Y H:i') }}</td>
                            <td class="py-4 text-right">
                                <div class="inline-flex gap-2">
                                    @if(in_array($req->status, ['pending', 'verified']))
                                        <form method="POST" action="{{ route('admin.privacy.approve', $req->id) }}" onsubmit="return confirm('Approve request for {{ $req->email }}?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary py-1 px-3">Approve</button>
                                        </form>

                                        <button onclick="document.getElementById('reject-dialog-{{ $req->id }}').showModal()" class="btn btn-sm border py-1 px-3 rounded-lg" style="border-color:var(--border); color:var(--text)">
                                            Reject
                                        </button>

                                        {{-- Reject Modal --}}
                                        <dialog id="reject-dialog-{{ $req->id }}" class="card p-6 rounded-2xl border max-w-sm w-full" style="border-color:var(--border); background:var(--card-bg)">
                                            <form method="POST" action="{{ route('admin.privacy.reject', $req->id) }}">
                                                @csrf
                                                <h4 class="text-sm font-bold mb-3" style="color:var(--text)">Reject Request</h4>
                                                <div class="mb-4">
                                                    <label class="block text-xs mb-1" style="color:var(--text-muted)">Reason / Notes</label>
                                                    <textarea name="notes" required class="w-full text-sm rounded border p-2 focus:outline-none" style="background:var(--surface-2); border-color:var(--border); color:var(--text)"></textarea>
                                                </div>
                                                <div class="flex justify-end gap-2 text-xs">
                                                    <button type="button" onclick="document.getElementById('reject-dialog-{{ $req->id }}').close()" class="btn py-1 px-3 border rounded" style="border-color:var(--border); color:var(--text)">Cancel</button>
                                                    <button type="submit" class="btn py-1 px-3 btn-danger text-white rounded bg-red-600">Submit Reject</button>
                                                </div>
                                            </form>
                                        </dialog>
                                    @endif

                                    @if($req->request_type === 'export' && $req->status === 'completed' && $req->export_file_path)
                                        <a href="{{ route('admin.privacy.download', $req->id) }}" class="btn btn-sm border py-1 px-3 rounded-lg" style="border-color:var(--border); color:var(--text)">
                                            Download Export
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6" style="color:var(--text-muted)">No privacy requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection
