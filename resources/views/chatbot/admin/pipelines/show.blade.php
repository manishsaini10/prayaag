@extends('admin.layouts.app')

@section('title', $pipeline->name)

@section('content')
<div style="padding:24px;max-width:1000px;margin:0 auto">
    <a href="{{ route('admin.chatbot.pipelines.index') }}" style="color:#64748b;text-decoration:none;font-size:13px;margin-bottom:16px;display:inline-block">&larr; Back to pipelines</a>

    <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:24px">
        <div>
            <h1 style="font-size:24px;font-weight:800;margin:0">{{ $pipeline->name }}</h1>
            @if($pipeline->description)<p style="color:#64748b;margin:4px 0 0">{{ $pipeline->description }}</p>@endif
        </div>
        <div style="display:flex;gap:8px">
            <button onclick="document.getElementById('stage-modal').style.display='flex'" style="padding:8px 16px;border:none;border-radius:8px;background:#0b2545;color:#fff;cursor:pointer;font-weight:600;font-size:12px">+ Stage</button>
            <a href="{{ route('admin.chatbot.deals.kanban') }}?pipeline={{ $pipeline->id }}" style="padding:8px 16px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;color:#0b2545;text-decoration:none;font-weight:600;font-size:12px">Kanban Board</a>
        </div>
    </div>

    @if(session('success'))
        <div style="padding:12px 16px;background:#dcfce7;color:#166534;border-radius:8px;margin-bottom:16px;font-size:14px">{{ session('success') }}</div>
    @endif

    <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;padding:16px;margin-bottom:24px">
        <h3 style="font-size:14px;font-weight:700;margin:0 0 12px">Stages</h3>
        <div id="stages-container" style="display:flex;flex-direction:column;gap:8px">
            @foreach($pipeline->stages as $stage)
                <div class="stage-row" data-id="{{ $stage->id }}" style="display:flex;align-items:center;gap:12px;padding:10px 12px;background:#f8fafc;border-radius:8px;border-left:4px solid {{ $stage->color ?? '#6366f1' }}">
                    <span style="cursor:grab;color:#94a3b8;font-size:16px">⠿</span>
                    <div style="flex:1">
                        <span style="font-weight:600;font-size:13px">{{ $stage->name }}</span>
                        <span style="color:#64748b;font-size:12px;margin-left:8px">{{ $stage->deals_count ?? 0 }} deals</span>
                    </div>
                    <span style="font-size:11px;color:#64748b">{{ $stage->probability }}%</span>
                    <form method="POST" action="{{ route('admin.chatbot.pipelines.stages.destroy', [$pipeline, $stage]) }}" onsubmit="return confirm('Delete this stage?')" style="display:inline">
                        @csrf @method('DELETE')
                        <button type="submit" style="border:none;background:none;color:#ef4444;cursor:pointer;font-size:12px;font-weight:600">Delete</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    @if($pipeline->deals->count() > 0)
        <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;padding:16px">
            <h3 style="font-size:14px;font-weight:700;margin:0 0 12px">Deals ({{ $pipeline->deals->count() }})</h3>
            @foreach($pipeline->deals as $deal)
                <div style="padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:13px;display:flex;justify-content:space-between">
                    <div>
                        <a href="{{ route('admin.chatbot.deals.show', $deal) }}" style="color:#2563eb;text-decoration:none;font-weight:600">{{ $deal->title }}</a>
                        <span style="color:#64748b;margin-left:8px">{{ $deal->contact?->first_name }} {{ $deal->contact?->last_name }}</span>
                    </div>
                    <span style="font-weight:600">{{ number_format($deal->value, 2) }} {{ $deal->currency }}</span>
                </div>
            @endforeach
        </div>
    @endif
</div>

<div id="stage-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center" onclick="if(event.target===this)this.style.display='none'">
    <div style="background:#fff;border-radius:16px;padding:24px;width:90%;max-width:400px">
        <h2 style="font-size:18px;font-weight:800;margin:0 0 16px">Add Stage</h2>
        <form method="POST" action="{{ route('admin.chatbot.pipelines.stages.store', $pipeline) }}">
            @csrf
            <div style="margin-bottom:12px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Stage Name</label>
                <input type="text" name="name" required style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Color</label>
                    <input type="color" name="color" value="#6366f1" style="width:100%;height:36px;padding:2px;border:1px solid #e2e8f0;border-radius:8px;cursor:pointer">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Probability (%)</label>
                    <input type="number" name="probability" min="0" max="100" value="50" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
                </div>
            </div>
            <button type="submit" style="padding:10px 20px;border:none;border-radius:8px;background:#0b2545;color:#fff;font-weight:600;cursor:pointer;width:100%">Add Stage</button>
        </form>
    </div>
</div>
@endsection
