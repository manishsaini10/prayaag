@extends('admin.layouts.app')

@section('title', 'Visitor Tracking')

@section('content')
<div style="padding:24px;max-width:1200px;margin:0 auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
        <div>
            <h1 style="font-size:24px;font-weight:800;margin:0">Visitor Tracking</h1>
            <p style="color:#64748b;margin:4px 0 0">Monitor and analyze website visitors</p>
        </div>
        <a href="{{ route('admin.chatbot.visitors.live') }}" style="padding:8px 16px;border:none;border-radius:8px;background:#0b2545;color:#fff;text-decoration:none;font-weight:600">Live View</a>
    </div>

    @if(session('success'))
        <div style="padding:12px 16px;background:#dcfce7;color:#166534;border-radius:8px;margin-bottom:16px;font-size:14px">{{ session('success') }}</div>
    @endif

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:24px">
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:24px;font-weight:800;color:#0b2545">{{ $stats['total_visitors'] }}</div>
            <div style="font-size:12px;color:#64748b">Total Visitors</div>
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:24px;font-weight:800;color:#16a34a">{{ $stats['visitors_today'] }}</div>
            <div style="font-size:12px;color:#64748b">Today</div>
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:24px;font-weight:800;color:#2563eb">{{ $live['count'] }}</div>
            <div style="font-size:12px;color:#64748b">Online Now</div>
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:24px;font-weight:800;color:#a855f7">{{ $stats['total_page_views'] }}</div>
            <div style="font-size:12px;color:#64748b">Page Views</div>
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:24px;font-weight:800;color:#f59e0b">{{ $stats['unique_countries'] }}</div>
            <div style="font-size:12px;color:#64748b">Countries</div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <h3 style="font-size:14px;font-weight:700;margin:0 0 12px">Devices</h3>
            @foreach($stats['device_breakdown'] as $device => $count)
                <div style="display:flex;justify-content:space-between;padding:4px 0;font-size:13px">
                    <span style="color:#64748b">{{ $device }}</span>
                    <span style="font-weight:600">{{ $count }}</span>
                </div>
            @endforeach
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <h3 style="font-size:14px;font-weight:700;margin:0 0 12px">Browsers</h3>
            @foreach($stats['browser_breakdown'] as $browser => $count)
                <div style="display:flex;justify-content:space-between;padding:4px 0;font-size:13px">
                    <span style="color:#64748b">{{ $browser }}</span>
                    <span style="font-weight:600">{{ $count }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden">
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="background:#f8fafc;text-align:left">
                        <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Visitor</th>
                        <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Location</th>
                        <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Device</th>
                        <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Sessions</th>
                        <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">First Seen</th>
                        <th style="padding:12px 16px;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visitors as $v)
                        <tr style="border-top:1px solid #f1f5f9">
                            <td style="padding:12px 16px">
                                <div style="font-size:13px;font-weight:600">{{ $v->name ?? 'Anonymous' }}</div>
                                <div style="font-size:11px;color:#64748b">{{ $v->session_id }}</div>
                            </td>
                            <td style="padding:12px 16px;font-size:13px;color:#64748b">{{ $v->country ?? '—' }}{{ $v->city ? ", {$v->city}" : '' }}</td>
                            <td style="padding:12px 16px;font-size:13px;color:#64748b">{{ $v->device ?? '—' }} / {{ $v->browser ?? '—' }}</td>
                            <td style="padding:12px 16px;font-size:13px">{{ $v->sessions_count }}</td>
                            <td style="padding:12px 16px;font-size:12px;color:#64748b">{{ $v->created_at->diffForHumans() }}</td>
                            <td style="padding:12px 16px">
                                <a href="{{ route('admin.chatbot.visitors.show', $v) }}" style="color:#2563eb;text-decoration:none;font-size:12px;font-weight:600">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding:40px;text-align:center;color:#94a3b8;font-size:14px">No visitors tracked yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div style="margin-top:16px">{{ $visitors->links() }}</div>
</div>
@endsection
