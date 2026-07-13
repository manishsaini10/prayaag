@extends('admin.layouts.app')

@section('title', 'Company Detail')

@section('content')
<div style="padding:24px;max-width:1000px;margin:0 auto">
    <a href="{{ route('admin.chatbot.companies.index') }}" style="color:#64748b;text-decoration:none;font-size:13px;margin-bottom:16px;display:inline-block">&larr; Back to companies</a>

    <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:24px">
        <div>
            <h1 style="font-size:24px;font-weight:800;margin:0">{{ $company->name }}</h1>
            <p style="color:#64748b;margin:4px 0 0">{{ $company->industry ?? 'No industry' }}</p>
        </div>
        <form method="POST" action="{{ route('admin.chatbot.companies.destroy', $company) }}" onsubmit="return confirm('Delete this company?')" style="display:inline">
            @csrf @method('DELETE')
            <button type="submit" style="padding:8px 16px;border:1px solid #ef4444;border-radius:8px;background:#fff;color:#ef4444;cursor:pointer;font-weight:600;font-size:12px">Delete</button>
        </form>
    </div>

    @if(session('success'))
        <div style="padding:12px 16px;background:#dcfce7;color:#166534;border-radius:8px;margin-bottom:16px;font-size:14px">{{ session('success') }}</div>
    @endif

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;margin-bottom:24px">
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">Website</div>
            <div style="font-size:14px;font-weight:600;margin-top:4px">{{ $company->website ? "<a href=\"{$company->website}\" target=_blank style=color:#2563eb>" . e($company->website) . '</a>' : '—' }}</div>
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">Email</div>
            <div style="font-size:14px;font-weight:600;margin-top:4px">{{ $company->email ?? '—' }}</div>
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">Phone</div>
            <div style="font-size:14px;font-weight:600;margin-top:4px">{{ $company->phone ?? '—' }}</div>
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">City / Country</div>
            <div style="font-size:14px;font-weight:600;margin-top:4px">{{ $company->city ?? '—' }}, {{ $company->country ?? '—' }}</div>
        </div>
    </div>

    @if($company->contacts->count() > 0)
        <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;padding:16px;margin-bottom:24px">
            <h3 style="font-size:14px;font-weight:700;margin:0 0 12px">Contacts ({{ $company->contacts->count() }})</h3>
            @foreach($company->contacts as $c)
                <div style="padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:13px">
                    <a href="{{ route('admin.chatbot.contacts.show', $c) }}" style="color:#2563eb;text-decoration:none;font-weight:600">{{ $c->first_name }} {{ $c->last_name }}</a>
                    <span style="color:#64748b;margin-left:8px">{{ $c->email ?? '' }} {{ $c->phone ? "/ {$c->phone}" : '' }}</span>
                </div>
            @endforeach
        </div>
    @endif

    @if($company->deals->count() > 0)
        <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;padding:16px">
            <h3 style="font-size:14px;font-weight:700;margin:0 0 12px">Deals ({{ $company->deals->count() }})</h3>
            @foreach($company->deals as $deal)
                <div style="padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:13px;display:flex;justify-content:space-between">
                    <div>
                        <a href="{{ route('admin.chatbot.deals.show', $deal) }}" style="color:#2563eb;text-decoration:none;font-weight:600">{{ $deal->title }}</a>
                        <span style="color:#64748b;margin-left:8px">{{ $deal->stage->name ?? '—' }}</span>
                    </div>
                    <span style="font-weight:600">{{ number_format($deal->value, 2) }} {{ $deal->currency }}</span>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
