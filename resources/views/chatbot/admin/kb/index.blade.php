@php use App\Models\Chatbot\Enterprise\KbCategory; @endphp
@extends('admin.layouts.app')

@section('title', 'Knowledge Base')

@section('content')
<div style="padding:24px;max-width:1200px;margin:0 auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
        <div>
            <h1 style="font-size:24px;font-weight:800;margin:0">Knowledge Base</h1>
            <p style="color:#64748b;margin:4px 0 0">Manage documents and AI training data</p>
        </div>
        <div style="display:flex;gap:8px">
            <form method="POST" action="{{ route('admin.chatbot.kb.index-cms') }}" style="display:inline">
                @csrf
                <button type="submit" style="padding:8px 16px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;cursor:pointer;font-weight:600">Re-index CMS</button>
            </form>
            <a href="#upload-modal" onclick="document.getElementById('upload-modal').style.display='flex'" style="padding:8px 16px;border:none;border-radius:8px;background:#0b2545;color:#fff;text-decoration:none;font-weight:600">+ Upload</a>
        </div>
    </div>

    @if(session('success'))
        <div style="padding:12px 16px;background:#dcfce7;color:#166534;border-radius:8px;margin-bottom:16px;font-size:14px">{{ session('success') }}</div>
    @endif

    <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden">
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="background:#f8fafc;text-align:left">
                        <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Title</th>
                        <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Type</th>
                        <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Category</th>
                        <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Chunks</th>
                        <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Words</th>
                        <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Indexed</th>
                        <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                        <tr style="border-top:1px solid #f1f5f9">
                            <td style="padding:12px 16px;font-size:13px;font-weight:600">{{ $doc->title }}</td>
                            <td style="padding:12px 16px">
                                <span style="display:inline-block;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;background:#e0e7ff;color:#4338ca">{{ $doc->type }}</span>
                            </td>
                            <td style="padding:12px 16px;font-size:13px;color:#64748b">{{ $doc->category?->name ?? '—' }}</td>
                            <td style="padding:12px 16px;font-size:13px">{{ $doc->chunks_count }}</td>
                            <td style="padding:12px 16px;font-size:13px">{{ number_format($doc->word_count) }}</td>
                            <td style="padding:12px 16px;font-size:12px;color:#64748b">{{ $doc->indexed_at?->diffForHumans() ?? 'Never' }}</td>
                            <td style="padding:12px 16px">
                                <form method="POST" action="{{ route('admin.chatbot.kb.delete', $doc) }}" style="display:inline" onsubmit="return confirm('Delete this document?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="border:none;background:none;color:#ef4444;cursor:pointer;font-size:12px;font-weight:600">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:40px;text-align:center;color:#94a3b8;font-size:14px">No documents yet. Upload your first document to build the knowledge base.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div style="margin-top:16px">{{ $documents->links() }}</div>
</div>

<div id="upload-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center" onclick="if(event.target===this)this.style.display='none'">
    <div style="background:#fff;border-radius:16px;padding:24px;width:90%;max-width:480px">
        <h2 style="font-size:18px;font-weight:800;margin:0 0 16px">Upload Document</h2>
        <form method="POST" action="{{ route('admin.chatbot.kb.upload') }}" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom:12px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Title</label>
                <input type="text" name="title" required style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
            </div>
            <div style="margin-bottom:12px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Category</label>
                <select name="category_id" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
                    <option value="">No category</option>
                    @foreach(KbCategory::where('is_active', true)->get() as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">File (PDF, DOCX, XLSX, TXT, CSV)</label>
                <input type="file" name="file" accept=".pdf,.docx,.xlsx,.txt,.csv" required style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
            </div>
            <button type="submit" style="padding:10px 20px;border:none;border-radius:8px;background:#0b2545;color:#fff;font-weight:600;cursor:pointer;width:100%">Upload & Index</button>
        </form>
    </div>
</div>
@endsection
