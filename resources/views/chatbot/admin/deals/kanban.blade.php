@extends('admin.layouts.app')

@section('title', 'Deal Kanban Board')

@section('content')
<div style="padding:24px;max-width:100%;margin:0 auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
        <div>
            <h1 style="font-size:24px;font-weight:800;margin:0">Deal Kanban</h1>
            <p style="color:#64748b;margin:4px 0 0">Drag deals between stages</p>
        </div>
        <div style="display:flex;gap:8px">
            <a href="{{ route('admin.chatbot.deals.index') }}" style="padding:8px 16px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;color:#0b2545;text-decoration:none;font-weight:600;font-size:12px">List View</a>
        </div>
    </div>

    @if(session('success'))
        <div style="padding:12px 16px;background:#dcfce7;color:#166534;border-radius:8px;margin-bottom:16px;font-size:14px">{{ session('success') }}</div>
    @endif

    @forelse($pipelines as $pipeline)
        <h3 style="font-size:16px;font-weight:700;margin-bottom:12px">{{ $pipeline->name }}</h3>
        <div style="display:flex;gap:12px;overflow-x:auto;padding-bottom:16px;margin-bottom:24px">
            @foreach($pipeline->stages as $stage)
                <div style="min-width:280px;flex-shrink:0;background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;padding:12px">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
                        <div style="display:flex;align-items:center;gap:8px">
                            <span style="width:10px;height:10px;border-radius:50%;background:{{ $stage->color ?? '#6366f1' }}"></span>
                            <span style="font-weight:700;font-size:13px">{{ $stage->name }}</span>
                            <span style="color:#64748b;font-size:12px">({{ $stage->deals->count() }})</span>
                        </div>
                        <span style="font-size:11px;color:#64748b">{{ $stage->probability }}%</span>
                    </div>
                    <div class="kanban-column" data-stage-id="{{ $stage->id }}" style="min-height:200px;display:flex;flex-direction:column;gap:8px">
                        @foreach($stage->deals as $deal)
                            <div class="kanban-card" data-deal-id="{{ $deal->id }}" draggable="true" style="background:#fff;border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;cursor:grab">
                                <div style="font-weight:600;font-size:13px">{{ $deal->title }}</div>
                                <div style="font-size:11px;color:#64748b;margin-top:4px">{{ $deal->contact?->first_name ?? 'No contact' }}</div>
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:6px">
                                    <span style="font-weight:700;font-size:13px">{{ number_format($deal->value, 0) }} {{ $deal->currency }}</span>
                                    <span style="font-size:10px;color:#94a3b8">{{ $deal->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @empty
        <div style="padding:40px;text-align:center;color:#94a3b8;font-size:14px;background:#fff;border-radius:16px;border:1px solid #e2e8f0">No active pipelines found. Create a pipeline first.</div>
    @endforelse
</div>

<script>
(function() {
    let dragCard = null;
    document.addEventListener('dragstart', function(e) {
        const card = e.target.closest('.kanban-card');
        if (!card) return;
        dragCard = card;
        card.style.opacity = '0.5';
        e.dataTransfer.effectAllowed = 'move';
    });
    document.addEventListener('dragend', function(e) {
        const card = e.target.closest('.kanban-card');
        if (!card) return;
        card.style.opacity = '1';
        dragCard = null;
        document.querySelectorAll('.kanban-column').forEach(col => col.style.background = '');
    });
    document.addEventListener('dragover', function(e) {
        const col = e.target.closest('.kanban-column');
        if (!col || !dragCard) return;
        e.preventDefault();
        col.style.background = '#f0f9ff';
    });
    document.addEventListener('dragleave', function(e) {
        const col = e.target.closest('.kanban-column');
        if (!col) return;
        col.style.background = '';
    });
    document.addEventListener('drop', function(e) {
        const col = e.target.closest('.kanban-column');
        if (!col || !dragCard) return;
        e.preventDefault();
        col.style.background = '';
        const stageId = col.dataset.stageId;
        const dealId = dragCard.dataset.dealId;
        if (!stageId || !dealId) return;
        col.appendChild(dragCard);
        fetch('/admin/chatbot/deals/' + dealId + '/move', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
            body: JSON.stringify({ stage_id: stageId })
        });
    });
})();
</script>
@endsection
