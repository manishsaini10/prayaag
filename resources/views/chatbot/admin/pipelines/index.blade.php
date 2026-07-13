@extends('admin.layouts.app')

@section('title', 'Pipelines')

@section('content')
<div style="padding:24px;max-width:1000px;margin:0 auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
        <div>
            <h1 style="font-size:24px;font-weight:800;margin:0">Sales Pipelines</h1>
            <p style="color:#64748b;margin:4px 0 0">Manage deal stages and pipelines</p>
        </div>
        <button onclick="document.getElementById('create-modal').style.display='flex'" style="padding:8px 16px;border:none;border-radius:8px;background:#0b2545;color:#fff;cursor:pointer;font-weight:600">+ Pipeline</button>
    </div>

    @if(session('success'))
        <div style="padding:12px 16px;background:#dcfce7;color:#166534;border-radius:8px;margin-bottom:16px;font-size:14px">{{ session('success') }}</div>
    @endif

    @forelse($pipelines as $p)
        <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;padding:16px;margin-bottom:12px">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
                <div>
                    <h3 style="font-size:16px;font-weight:700;margin:0">{{ $p->name }}</h3>
                    @if($p->description)<p style="color:#64748b;font-size:13px;margin:4px 0 0">{{ $p->description }}</p>@endif
                </div>
                <div style="display:flex;gap:8px;align-items:center">
                    <span style="font-size:12px;color:#64748b">{{ $p->deals_count }} deals</span>
                    <a href="{{ route('admin.chatbot.pipelines.show', $p) }}" style="padding:6px 12px;border:1px solid #e2e8f0;border-radius:6px;color:#0b2545;text-decoration:none;font-size:12px;font-weight:600">Manage</a>
                </div>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
                @foreach($p->stages as $s)
                    <div style="display:flex;align-items:center;gap:6px;padding:6px 12px;background:#f8fafc;border-radius:8px;border-left:3px solid {{ $s->color ?? '#6366f1' }};font-size:12px">
                        <span>{{ $s->name }}</span>
                        <span style="color:#64748b;font-size:11px">{{ $s->deals_count ?? 0 }} deals</span>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div style="padding:40px;text-align:center;color:#94a3b8;font-size:14px;background:#fff;border-radius:16px;border:1px solid #e2e8f0">No pipelines defined yet.</div>
    @endforelse
</div>

<div id="create-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center" onclick="if(event.target===this)this.style.display='none'">
    <div style="background:#fff;border-radius:16px;padding:24px;width:90%;max-width:400px">
        <h2 style="font-size:18px;font-weight:800;margin:0 0 16px">Create Pipeline</h2>
        <form method="POST" action="{{ route('admin.chatbot.pipelines.store') }}">
            @csrf
            <div style="margin-bottom:12px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Pipeline Name</label>
                <input type="text" name="name" required style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
            </div>
            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Description</label>
                <textarea name="description" rows="2" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px"></textarea>
            </div>
            <button type="submit" style="padding:10px 20px;border:none;border-radius:8px;background:#0b2545;color:#fff;font-weight:600;cursor:pointer;width:100%">Create Pipeline</button>
        </form>
    </div>
</div>
@endsection
