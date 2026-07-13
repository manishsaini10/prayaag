@extends('admin.layouts.app')

@section('title', 'Deals')

@section('content')
<div style="padding:24px;max-width:1200px;margin:0 auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
        <div>
            <h1 style="font-size:24px;font-weight:800;margin:0">Deals</h1>
            <p style="color:#64748b;margin:4px 0 0">{{ $deals->total() }} deals</p>
        </div>
        <div style="display:flex;gap:8px">
            <a href="{{ route('admin.chatbot.deals.kanban') }}" style="padding:8px 16px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;color:#0b2545;text-decoration:none;font-weight:600;font-size:12px">Kanban Board</a>
            <button onclick="document.getElementById('create-modal').style.display='flex'" style="padding:8px 16px;border:none;border-radius:8px;background:#0b2545;color:#fff;cursor:pointer;font-weight:600">+ Deal</button>
        </div>
    </div>

    @if(session('success'))
        <div style="padding:12px 16px;background:#dcfce7;color:#166534;border-radius:8px;margin-bottom:16px;font-size:14px">{{ session('success') }}</div>
    @endif

    <div style="display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap">
        <select onchange="if(this.value) window.location=this.value" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
            <option value="{{ route('admin.chatbot.deals.index') }}">All Pipelines</option>
            @foreach($pipelines as $p)
                <option value="{{ route('admin.chatbot.deals.index') }}?pipeline_id={{ $p->id }}" {{ request('pipeline_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
            @endforeach
        </select>
        <select onchange="if(this.value) window.location=this.value" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
            <option value="{{ route('admin.chatbot.deals.index') }}">All Status</option>
            <option value="{{ route('admin.chatbot.deals.index') }}?status=open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
            <option value="{{ route('admin.chatbot.deals.index') }}?status=won" {{ request('status') === 'won' ? 'selected' : '' }}>Won</option>
            <option value="{{ route('admin.chatbot.deals.index') }}?status=lost" {{ request('status') === 'lost' ? 'selected' : '' }}>Lost</option>
        </select>
    </div>

    <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden">
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#f8fafc;text-align:left">
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Title</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Contact</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Pipeline / Stage</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Value</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Status</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deals as $d)
                    <tr style="border-top:1px solid #f1f5f9">
                        <td style="padding:12px 16px;font-weight:600;font-size:13px">{{ $d->title }}</td>
                        <td style="padding:12px 16px;font-size:13px;color:#64748b">{{ $d->contact?->first_name }} {{ $d->contact?->last_name ?? '—' }}</td>
                        <td style="padding:12px 16px;font-size:12px">
                            <span style="color:#64748b">{{ $d->pipeline?->name }} / </span>
                            <span style="background:#f0fdf4;color:#166534;padding:2px 8px;border-radius:4px;font-weight:600">{{ $d->stage?->name }}</span>
                        </td>
                        <td style="padding:12px 16px;font-weight:600;font-size:13px">{{ number_format($d->value, 0) }} {{ $d->currency }}</td>
                        <td style="padding:12px 16px">
                            <span style="background:{{ $d->status === 'won' ? '#dcfce7' : ($d->status === 'lost' ? '#fee2e2' : '#fef3c7') }};color:{{ $d->status === 'won' ? '#166534' : ($d->status === 'lost' ? '#991b1b' : '#92400e') }};padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600">{{ $d->status }}</span>
                        </td>
                        <td style="padding:12px 16px">
                            <a href="{{ route('admin.chatbot.deals.show', $d) }}" style="color:#2563eb;text-decoration:none;font-size:12px;font-weight:600">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="padding:40px;text-align:center;color:#94a3b8;font-size:14px">No deals yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px">{{ $deals->links() }}</div>
</div>

<div id="create-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center" onclick="if(event.target===this)this.style.display='none'">
    <div style="background:#fff;border-radius:16px;padding:24px;width:90%;max-width:480px">
        <h2 style="font-size:18px;font-weight:800;margin:0 0 16px">Create Deal</h2>
        <form method="POST" action="{{ route('admin.chatbot.deals.store') }}">
            @csrf
            <div style="margin-bottom:12px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Deal Title</label>
                <input type="text" name="title" required style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Contact</label>
                    <select name="contact_id" required style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
                        <option value="">Select contact</option>
                        @foreach(\App\Models\Chatbot\Enterprise\Contact::orderBy('first_name')->get() as $c)
                            <option value="{{ $c->id }}">{{ $c->first_name }} {{ $c->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Pipeline Stage</label>
                    <select name="stage_id" required style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
                        <option value="">Select stage</option>
                        @foreach(\App\Models\Chatbot\Enterprise\Pipeline::with('stages')->where('is_active', true)->get() as $p)
                            @foreach($p->stages as $s)
                                <option value="{{ $s->id }}">{{ $p->name }} / {{ $s->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Value</label>
                    <input type="number" name="value" step="0.01" min="0" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Expected Close Date</label>
                    <input type="date" name="expected_close_date" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
                </div>
            </div>
            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Notes</label>
                <textarea name="notes" rows="2" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px"></textarea>
            </div>
            <button type="submit" style="padding:10px 20px;border:none;border-radius:8px;background:#0b2545;color:#fff;font-weight:600;cursor:pointer;width:100%">Create Deal</button>
        </form>
    </div>
</div>
@endsection
