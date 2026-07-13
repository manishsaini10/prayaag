@extends('admin.layouts.app')

@section('title', 'Canned Responses')

@section('content')
<div style="padding:24px;max-width:800px;margin:0 auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
        <div>
            <h1 style="font-size:24px;font-weight:800;margin:0">Canned Responses</h1>
            <p style="color:#64748b;margin:4px 0 0">Pre-written replies for quick ticket responses</p>
        </div>
        <div style="display:flex;gap:8px">
            <a href="{{ route('admin.chatbot.tickets.index') }}" style="padding:8px 16px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;color:#0b2545;text-decoration:none;font-weight:600;font-size:12px">&larr; Tickets</a>
            <button onclick="document.getElementById('create-modal').style.display='flex'" style="padding:8px 16px;border:none;border-radius:8px;background:#0b2545;color:#fff;cursor:pointer;font-weight:600">+ Response</button>
        </div>
    </div>

    @if(session('success'))
        <div style="padding:12px 16px;background:#dcfce7;color:#166534;border-radius:8px;margin-bottom:16px;font-size:14px">{{ session('success') }}</div>
    @endif

    @forelse($responses as $r)
        <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:14px 16px;margin-bottom:8px">
            <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:6px">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                    <code style="background:#e0e7ff;color:#4338ca;padding:2px 8px;border-radius:4px;font-size:12px;font-weight:600">/{{ $r->shortcut }}</code>
                    @if($r->category)<span style="background:#f1f5f9;color:#64748b;padding:2px 6px;border-radius:4px;font-size:11px">{{ $r->category }}</span>@endif
                    @if($r->department)<span style="font-size:11px;color:#94a3b8">{{ $r->department->name }}</span>@endif
                </div>
                <button onclick="this.parentElement.parentElement.querySelector('.reply-body').classList.toggle('expanded')" style="border:none;background:none;color:#64748b;cursor:pointer;font-size:11px">Toggle</button>
            </div>
            <div class="reply-body" style="font-size:13px;color:#475569;white-space:pre-wrap;max-height:60px;overflow:hidden;cursor:pointer" onclick="this.classList.toggle('expanded')">{{ $r->body }}</div>
            <div style="margin-top:8px;display:flex;gap:6px">
                <button onclick="navigator.clipboard.writeText('/{{ $r->shortcut }}')" style="padding:4px 10px;border:1px solid #e2e8f0;border-radius:6px;background:#fff;font-size:11px;cursor:pointer">Copy shortcut</button>
            </div>
        </div>
    @empty
        <div style="padding:40px;text-align:center;color:#94a3b8;font-size:14px;background:#fff;border-radius:12px;border:1px solid #e2e8f0">No canned responses yet.</div>
    @endforelse
</div>

<div id="create-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center" onclick="if(event.target===this)this.style.display='none'">
    <div style="background:#fff;border-radius:16px;padding:24px;width:90%;max-width:480px">
        <h2 style="font-size:18px;font-weight:800;margin:0 0 16px">Create Canned Response</h2>
        <form method="POST" action="{{ route('admin.chatbot.canned.store') }}">
            @csrf
            <div style="margin-bottom:12px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Shortcut</label>
                <input type="text" name="shortcut" placeholder="greeting" required style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
                <div style="font-size:11px;color:#94a3b8;margin-top:2px">Type /shortcut in ticket replies to use</div>
            </div>
            <div style="margin-bottom:12px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Body</label>
                <textarea name="body" rows="4" required style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px"></textarea>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Category</label>
                    <input type="text" name="category" placeholder="billing, support" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Department</label>
                    <select name="department_id" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
                        <option value="">All departments</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" style="padding:10px 20px;border:none;border-radius:8px;background:#0b2545;color:#fff;font-weight:600;cursor:pointer;width:100%">Create</button>
        </form>
    </div>
</div>
@endsection
