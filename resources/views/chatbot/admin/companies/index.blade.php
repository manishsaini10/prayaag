@extends('admin.layouts.app')

@section('title', 'Companies')

@section('content')
<div style="padding:24px;max-width:1000px;margin:0 auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
        <div>
            <h1 style="font-size:24px;font-weight:800;margin:0">Companies</h1>
            <p style="color:#64748b;margin:4px 0 0">{{ $companies->total() }} companies</p>
        </div>
        <button onclick="document.getElementById('create-modal').style.display='flex'" style="padding:8px 16px;border:none;border-radius:8px;background:#0b2545;color:#fff;cursor:pointer;font-weight:600">+ Company</button>
    </div>

    @if(session('success'))
        <div style="padding:12px 16px;background:#dcfce7;color:#166534;border-radius:8px;margin-bottom:16px;font-size:14px">{{ session('success') }}</div>
    @endif

    <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden">
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#f8fafc;text-align:left">
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Name</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Industry</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Contacts</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">City</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $comp)
                    <tr style="border-top:1px solid #f1f5f9">
                        <td style="padding:12px 16px;font-weight:600;font-size:13px">{{ $comp->name }}</td>
                        <td style="padding:12px 16px;font-size:13px;color:#64748b">{{ $comp->industry ?? '—' }}</td>
                        <td style="padding:12px 16px;font-size:13px">{{ $comp->contacts_count }}</td>
                        <td style="padding:12px 16px;font-size:13px;color:#64748b">{{ $comp->city ?? '—' }}</td>
                        <td style="padding:12px 16px">
                            <a href="{{ route('admin.chatbot.companies.show', $comp) }}" style="color:#2563eb;text-decoration:none;font-size:12px;font-weight:600">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="padding:40px;text-align:center;color:#94a3b8;font-size:14px">No companies yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px">{{ $companies->links() }}</div>
</div>

<div id="create-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center" onclick="if(event.target===this)this.style.display='none'">
    <div style="background:#fff;border-radius:16px;padding:24px;width:90%;max-width:480px">
        <h2 style="font-size:18px;font-weight:800;margin:0 0 16px">Create Company</h2>
        <form method="POST" action="{{ route('admin.chatbot.companies.store') }}">
            @csrf
            <div style="margin-bottom:12px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Company Name</label>
                <input type="text" name="name" required style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Industry</label>
                    <input type="text" name="industry" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Website</label>
                    <input type="url" name="website" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
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
            <button type="submit" style="padding:10px 20px;border:none;border-radius:8px;background:#0b2545;color:#fff;font-weight:600;cursor:pointer;width:100%">Create Company</button>
        </form>
    </div>
</div>
@endsection
