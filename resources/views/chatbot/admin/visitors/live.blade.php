@extends('admin.layouts.app')

@section('title', 'Live Visitors')

@section('content')
<div style="padding:24px;max-width:1000px;margin:0 auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
        <div>
            <h1 style="font-size:24px;font-weight:800;margin:0">Live Visitors</h1>
            <p id="live-count" style="color:#64748b;margin:4px 0 0">Monitoring visitors in real-time</p>
        </div>
        <a href="{{ route('admin.chatbot.visitors.index') }}" style="color:#64748b;text-decoration:none;font-size:13px">&larr; All Visitors</a>
    </div>

    <div id="live-list">
        @forelse($live['visitors'] as $v)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;background:#fff;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:8px">
                <div style="display:flex;align-items:center;gap:12px">
                    <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#22c55e"></span>
                    <div>
                        <div style="font-weight:600;font-size:14px">{{ $v['name'] }}</div>
                        <div style="font-size:11px;color:#64748b">{{ $v['current_page'] ?? '—' }}</div>
                    </div>
                </div>
                <div style="display:flex;gap:16px;align-items:center">
                    <span style="font-size:11px;color:#64748b">{{ $v['device'] ?? '—' }}</span>
                    <span style="font-size:11px;color:#64748b">{{ $v['country'] ?? '—' }}</span>
                    <span style="font-size:11px;color:#64748b">{{ gmdate('i:s', $v['duration']) }}</span>
                    <span style="font-size:11px;color:#64748b">{{ $v['page_views'] }} pages</span>
                    <a href="{{ route('admin.chatbot.visitors.show', $v['id']) }}" style="color:#2563eb;text-decoration:none;font-size:12px;font-weight:600">View</a>
                </div>
            </div>
        @empty
            <div style="padding:40px;text-align:center;color:#94a3b8;font-size:14px;background:#fff;border-radius:12px;border:1px solid #e2e8f0">No visitors online right now.</div>
        @endforelse
    </div>
</div>

<script>
(function() {
    function refreshLive() {
        fetch('{{ route('admin.chatbot.visitors.live-data') }}')
            .then(r => r.json())
            .then(data => {
                const el = document.getElementById('live-list');
                const countEl = document.getElementById('live-count');
                countEl.textContent = data.count + ' visitor(s) online';
                if (data.visitors.length === 0) {
                    el.innerHTML = '<div style="padding:40px;text-align:center;color:#94a3b8;font-size:14px;background:#fff;border-radius:12px;border:1px solid #e2e8f0">No visitors online right now.</div>';
                    return;
                }
                el.innerHTML = data.visitors.map(v => `
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;background:#fff;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:8px">
                        <div style="display:flex;align-items:center;gap:12px">
                            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#22c55e"></span>
                            <div>
                                <div style="font-weight:600;font-size:14px">${v.name}</div>
                                <div style="font-size:11px;color:#64748b">${v.current_page || '—'}</div>
                            </div>
                        </div>
                        <div style="display:flex;gap:16px;align-items:center">
                            <span style="font-size:11px;color:#64748b">${v.device || '—'}</span>
                            <span style="font-size:11px;color:#64748b">${v.country || '—'}</span>
                            <span style="font-size:11px;color:#64748b">${Math.floor(v.duration/60)}:${String(v.duration%60).padStart(2,'0')}</span>
                            <span style="font-size:11px;color:#64748b">${v.page_views} pages</span>
                            <a href="{{ url('/admin/chatbot/visitors') }}/${v.id}" style="color:#2563eb;text-decoration:none;font-size:12px;font-weight:600">View</a>
                        </div>
                    </div>
                `).join('');
            });
    }
    setInterval(refreshLive, 10000);
})();
</script>
@endsection
