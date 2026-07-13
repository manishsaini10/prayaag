@extends('admin.layouts.app')

@section('title', $deal->title)

@section('content')
<div style="padding:24px;max-width:1000px;margin:0 auto">
    <a href="{{ route('admin.chatbot.deals.index') }}" style="color:#64748b;text-decoration:none;font-size:13px;margin-bottom:16px;display:inline-block">&larr; Back to deals</a>

    <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:24px">
        <div>
            <h1 style="font-size:24px;font-weight:800;margin:0">{{ $deal->title }}</h1>
            <p style="color:#64748b;margin:4px 0 0">{{ $deal->contact?->first_name }} {{ $deal->contact?->last_name }} {{ $deal->company ? "— {$deal->company->name}" : '' }}</p>
        </div>
        <div style="display:flex;gap:8px">
            <form method="POST" action="{{ route('admin.chatbot.deals.status', $deal) }}" style="display:inline">
                @csrf
                <input type="hidden" name="status" value="won">
                <button type="submit" style="padding:8px 16px;border:1px solid #16a34a;border-radius:8px;background:#fff;color:#16a34a;cursor:pointer;font-weight:600;font-size:12px">Mark Won</button>
            </form>
            <button onclick="document.getElementById('lost-modal').style.display='flex'" style="padding:8px 16px;border:1px solid #ef4444;border-radius:8px;background:#fff;color:#ef4444;cursor:pointer;font-weight:600;font-size:12px">Mark Lost</button>
            <form method="POST" action="{{ route('admin.chatbot.deals.destroy', $deal) }}" onsubmit="return confirm('Delete this deal?')" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" style="padding:8px 16px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;color:#64748b;cursor:pointer;font-weight:600;font-size:12px">Delete</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div style="padding:12px 16px;background:#dcfce7;color:#166534;border-radius:8px;margin-bottom:16px;font-size:14px">{{ session('success') }}</div>
    @endif

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;margin-bottom:24px">
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">Value</div>
            <div style="font-size:20px;font-weight:800;margin-top:4px">{{ number_format($deal->value, 2) }} {{ $deal->currency }}</div>
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">Stage</div>
            <div style="font-size:14px;font-weight:600;margin-top:4px">
                <span style="background:{{ $deal->stage?->color ?? '#6366f1' }}20;color:{{ $deal->stage?->color ?? '#6366f1' }};padding:2px 8px;border-radius:4px">{{ $deal->stage?->name }}</span>
            </div>
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">Pipeline</div>
            <div style="font-size:14px;font-weight:600;margin-top:4px">{{ $deal->pipeline?->name }}</div>
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">Status</div>
            <div style="font-size:14px;font-weight:600;margin-top:4px;text-transform:capitalize">{{ $deal->status }}</div>
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">Probability</div>
            <div style="font-size:14px;font-weight:600;margin-top:4px">{{ $deal->probability }}%</div>
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">Expected Close</div>
            <div style="font-size:14px;font-weight:600;margin-top:4px">{{ $deal->expected_close_date ? \Carbon\Carbon::parse($deal->expected_close_date)->format('M j, Y') : '—' }}</div>
        </div>
    </div>

    @if($deal->notes)
        <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;padding:16px;margin-bottom:24px">
            <h3 style="font-size:14px;font-weight:700;margin:0 0 8px">Notes</h3>
            <p style="font-size:13px;color:#475569;white-space:pre-wrap">{{ $deal->notes }}</p>
        </div>
    @endif

    @if($deal->lost_reason)
        <div style="background:#fef2f2;border-radius:16px;border:1px solid #fee2e2;padding:16px;margin-bottom:24px">
            <h3 style="font-size:14px;font-weight:700;margin:0 0 4px;color:#991b1b">Lost Reason</h3>
            <p style="font-size:13px;color:#991b1b">{{ $deal->lost_reason }}</p>
        </div>
    @endif
</div>

<div id="lost-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center" onclick="if(event.target===this)this.style.display='none'">
    <div style="background:#fff;border-radius:16px;padding:24px;width:90%;max-width:400px">
        <h2 style="font-size:18px;font-weight:800;margin:0 0 16px;color:#ef4444">Mark as Lost</h2>
        <form method="POST" action="{{ route('admin.chatbot.deals.status', $deal) }}">
            @csrf
            <input type="hidden" name="status" value="lost">
            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Reason (optional)</label>
                <textarea name="lost_reason" rows="3" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px" placeholder="Why was this deal lost?"></textarea>
            </div>
            <button type="submit" style="padding:10px 20px;border:none;border-radius:8px;background:#ef4444;color:#fff;font-weight:600;cursor:pointer;width:100%">Mark as Lost</button>
        </form>
    </div>
</div>
@endsection
