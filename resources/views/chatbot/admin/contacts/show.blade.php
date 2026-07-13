@extends('admin.layouts.app')

@section('title', 'Contact Detail')

@section('content')
<div style="padding:24px;max-width:1000px;margin:0 auto">
    <a href="{{ route('admin.chatbot.contacts.index') }}" style="color:#64748b;text-decoration:none;font-size:13px;margin-bottom:16px;display:inline-block">&larr; Back to contacts</a>

    <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:24px">
        <div>
            <h1 style="font-size:24px;font-weight:800;margin:0">{{ $contact->first_name }} {{ $contact->last_name }}</h1>
            <p style="color:#64748b;margin:4px 0 0">{{ $contact->email ?? 'No email' }} &middot; {{ $contact->phone ?? 'No phone' }}</p>
        </div>
        <div style="display:flex;gap:8px">
            <a href="#edit-modal" onclick="document.getElementById('edit-modal').style.display='flex'" style="padding:8px 16px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;color:#0b2545;text-decoration:none;font-weight:600;font-size:12px">Edit</a>
            <form method="POST" action="{{ route('admin.chatbot.contacts.destroy', $contact) }}" onsubmit="return confirm('Delete this contact?')" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" style="padding:8px 16px;border:1px solid #ef4444;border-radius:8px;background:#fff;color:#ef4444;cursor:pointer;font-weight:600;font-size:12px">Delete</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div style="padding:12px 16px;background:#dcfce7;color:#166534;border-radius:8px;margin-bottom:16px;font-size:14px">{{ session('success') }}</div>
    @endif

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;margin-bottom:24px">
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">Source</div>
            <div style="font-size:14px;font-weight:600;margin-top:4px;text-transform:capitalize">{{ $contact->source }}</div>
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">Status</div>
            <div style="font-size:14px;font-weight:600;margin-top:4px;text-transform:capitalize">{{ $contact->status }}</div>
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">Company</div>
            <div style="font-size:14px;font-weight:600;margin-top:4px">{{ $contact->company ?? '—' }}</div>
        </div>
        <div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600">Position</div>
            <div style="font-size:14px;font-weight:600;margin-top:4px">{{ $contact->position ?? '—' }}</div>
        </div>
    </div>

    @if($contact->companies->count() > 0)
        <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;padding:16px;margin-bottom:24px">
            <h3 style="font-size:14px;font-weight:700;margin:0 0 12px">Linked Companies</h3>
            @foreach($contact->companies as $comp)
                <div style="padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:13px">
                    <a href="{{ route('admin.chatbot.companies.show', $comp) }}" style="color:#2563eb;text-decoration:none;font-weight:600">{{ $comp->name }}</a>
                    <span style="color:#64748b;margin-left:8px">{{ $comp->industry ?? '' }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;padding:16px">
        <h3 style="font-size:14px;font-weight:700;margin:0 0 12px">Notes</h3>
        <form method="POST" action="{{ route('admin.chatbot.contacts.notes', $contact) }}" style="margin-bottom:12px">
            @csrf
            <div style="display:flex;gap:8px">
                <input type="text" name="body" placeholder="Add a note..." required style="flex:1;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
                <button type="submit" style="padding:8px 16px;border:none;border-radius:8px;background:#0b2545;color:#fff;font-weight:600;cursor:pointer;font-size:12px">Add</button>
            </div>
        </form>
        @if($contact->notes && $contact->notes->count() > 0)
            @foreach($contact->notes as $note)
                <div style="padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:13px">
                    <div>{{ $note->body }}</div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:2px">{{ $note->created_at->diffForHumans() }}</div>
                </div>
            @endforeach
        @else
            <p style="color:#94a3b8;font-size:13px">No notes yet.</p>
        @endif
    </div>
</div>

<div id="edit-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center" onclick="if(event.target===this)this.style.display='none'">
    <div style="background:#fff;border-radius:16px;padding:24px;width:90%;max-width:480px">
        <h2 style="font-size:18px;font-weight:800;margin:0 0 16px">Edit Contact</h2>
        <form method="POST" action="{{ route('admin.chatbot.contacts.update', $contact) }}">
            @csrf @method('PUT')
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">First Name</label>
                    <input type="text" name="first_name" value="{{ $contact->first_name }}" required style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Last Name</label>
                    <input type="text" name="last_name" value="{{ $contact->last_name }}" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Email</label>
                    <input type="email" name="email" value="{{ $contact->email }}" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Phone</label>
                    <input type="text" name="phone" value="{{ $contact->phone }}" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
                </div>
            </div>
            <div style="margin-bottom:12px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px">Status</label>
                <select name="status" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
                    <option value="lead" {{ $contact->status === 'lead' ? 'selected' : '' }}>Lead</option>
                    <option value="active" {{ $contact->status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $contact->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <button type="submit" style="padding:10px 20px;border:none;border-radius:8px;background:#0b2545;color:#fff;font-weight:600;cursor:pointer;width:100%">Update Contact</button>
        </form>
    </div>
</div>
@endsection
