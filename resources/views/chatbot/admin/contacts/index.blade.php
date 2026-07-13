@extends('admin.layouts.app')

@section('title', 'Contacts')

@section('content')
<div style="padding:24px;max-width:1200px;margin:0 auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
        <div>
            <h1 style="font-size:24px;font-weight:800;margin:0">Contacts</h1>
            <p style="color:#64748b;margin:4px 0 0">{{ $contacts->total() }} total contacts</p>
        </div>
        <button onclick="document.getElementById('create-modal').style.display='flex'" style="padding:8px 16px;border:none;border-radius:8px;background:#0b2545;color:#fff;cursor:pointer;font-weight:600">+ Contact</button>
    </div>

    @if(session('success'))
        <div style="padding:12px 16px;background:#dcfce7;color:#166534;border-radius:8px;margin-bottom:16px;font-size:14px">{{ session('success') }}</div>
    @endif

    <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden">
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#f8fafc;text-align:left">
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Name</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Email</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Phone</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Source</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Status</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Created</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contacts as $c)
                    <tr style="border-top:1px solid #f1f5f9">
                        <td style="padding:12px 16px;font-weight:600;font-size:13px">{{ $c->first_name }} {{ $c->last_name }}</td>
                        <td style="padding:12px 16px;font-size:13px;color:#64748b">{{ $c->email ?? '—' }}</td>
                        <td style="padding:12px 16px;font-size:13px;color:#64748b">{{ $c->phone ?? '—' }}</td>
                        <td style="padding:12px 16px;font-size:12px"><span style="background:#e0e7ff;color:#4338ca;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600">{{ $c->source }}</span></td>
                        <td style="padding:12px 16px;font-size:12px">
                            <span style="background:{{ $c->status === 'lead' ? '#fef3c7' : ($c->status === 'active' ? '#dcfce7' : '#f1f5f9') }};color:{{ $c->status === 'lead' ? '#92400e' : ($c->status === 'active' ? '#166534' : '#64748b') }};padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600">{{ $c->status }}</span>
                        </td>
                        <td style="padding:12px 16px;font-size:12px;color:#64748b">{{ $c->created_at->diffForHumans() }}</td>
                        <td style="padding:12px 16px">
                            <a href="{{ route('admin.chatbot.contacts.show', $c) }}" style="color:#2563eb;text-decoration:none;font-size:12px;font-weight:600">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="padding:40px;text-align:center;color:#94a3b8;font-size:14px">No contacts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px">{{ $contacts->links() }}</div>
</div>

<div id="create-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center" onclick="if(event.target===this)this.style.display='none'">
    <div style="background:#fff;border-radius:16px;padding:24px;width:90%;max-width:480px">
        <h2 style="font-size:18px;font-weight:800;margin:0 0 16px">Create Contact</h2>
        <form method="POST" action="{{ route('admin.chatbot.contacts.store') }}">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">First Name</label>
                    <input type="text" name="first_name" required style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Last Name</label>
                    <input type="text" name="last_name" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Email</label>
                    <input type="email" name="email" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Phone</label>
                    <input type="text" name="phone" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
                </div>
            </div>
            <div style="margin-bottom:12px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Company</label>
                <input type="text" name="company" placeholder="Organization name" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
            </div>
            <div style="margin-bottom:12px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Position</label>
                <input type="text" name="position" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
            </div>
            <button type="submit" style="padding:10px 20px;border:none;border-radius:8px;background:#0b2545;color:#fff;font-weight:600;cursor:pointer;width:100%">Create Contact</button>
        </form>
    </div>
</div>
@endsection
