@extends('admin.layouts.app')

@section('title', 'KB Categories')

@section('content')
<div style="padding:24px;max-width:800px;margin:0 auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
        <div>
            <h1 style="font-size:24px;font-weight:800;margin:0">KB Categories</h1>
            <p style="color:#64748b;margin:4px 0 0">Organize knowledge base documents</p>
        </div>
        <button onclick="document.getElementById('create-modal').style.display='flex'" style="padding:8px 16px;border:none;border-radius:8px;background:#0b2545;color:#fff;cursor:pointer;font-weight:600">+ Category</button>
    </div>

    @if(session('success'))
        <div style="padding:12px 16px;background:#dcfce7;color:#166534;border-radius:8px;margin-bottom:16px;font-size:14px">{{ session('success') }}</div>
    @endif

    <div style="display:flex;flex-direction:column;gap:8px">
        @forelse($categories as $cat)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;background:#fff;border-radius:12px;border:1px solid #e2e8f0">
                <div>
                    <div style="font-weight:600;font-size:14px">{{ $cat->name }}</div>
                    <div style="font-size:12px;color:#64748b">{{ $cat->documents_count ?? 0 }} documents</div>
                </div>
                <form method="POST" action="{{ route('admin.chatbot.kb.categories.destroy', $cat) }}" onsubmit="return confirm('Delete this category?')" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="submit" style="border:none;background:none;color:#ef4444;cursor:pointer;font-size:12px;font-weight:600">Delete</button>
                </form>
            </div>
        @empty
            <div style="padding:40px;text-align:center;color:#94a3b8;font-size:14px">No categories yet.</div>
        @endforelse
    </div>
</div>

<div id="create-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center" onclick="if(event.target===this)this.style.display='none'">
    <div style="background:#fff;border-radius:16px;padding:24px;width:90%;max-width:400px">
        <h2 style="font-size:18px;font-weight:800;margin:0 0 16px">Create Category</h2>
        <form method="POST" action="{{ route('admin.chatbot.kb.categories.store') }}">
            @csrf
            <div style="margin-bottom:12px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Name</label>
                <input type="text" name="name" required style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
            </div>
            <div style="margin-bottom:12px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Description</label>
                <textarea name="description" rows="2" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px"></textarea>
            </div>
            <button type="submit" style="padding:10px 20px;border:none;border-radius:8px;background:#0b2545;color:#fff;font-weight:600;cursor:pointer;width:100%">Create</button>
        </form>
    </div>
</div>
@endsection
