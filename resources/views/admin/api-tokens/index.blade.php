@extends('admin.layout')
@section('title', 'API Tokens')
@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="px-4 py-3 rounded-xl text-sm" style="background:#dcfce7;color:#166534">{{ session('success') }}</div>
        @if(session('token'))
            <div class="px-4 py-3 rounded-xl text-sm" style="background:#fef3c7;color:#92400e;word-break:break-all">
                <strong>New API Token:</strong> {{ session('token') }}<br>
                <span class="text-xs">Copy this token now. It won't be shown again.</span>
            </div>
        @endif
    @endif

    <div class="card p-6">
        <h3 class="text-sm font-semibold mb-4" style="color:var(--text)">Create New Token</h3>
        <form method="POST" action="{{ route('admin.api-tokens.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Token Name *</label>
                <input type="text" name="name" placeholder="e.g. Production API" required class="w-full">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Rate Limit (req/min)</label>
                <input type="number" name="rate_limit" value="60" min="1" max="10000" class="w-full">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Expires At</label>
                <input type="datetime-local" name="expires_at" class="w-full">
            </div>
            <div class="md:col-span-3">
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Permissions</label>
                <div class="flex flex-wrap gap-3">
                    @foreach(['read','write','delete','admin'] as $perm)
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="{{ $perm }}" {{ $perm === 'read' ? 'checked' : '' }}>
                            <span class="text-sm capitalize">{{ $perm }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="md:col-span-3">
                <button type="submit" class="btn-primary">Generate Token</button>
            </div>
        </form>
    </div>

    @if($tokens->isEmpty())
        <div class="card p-12 text-center">
            <p class="text-sm" style="color:var(--text-muted)">No API tokens created yet.</p>
        </div>
    @else
        <div class="card p-0 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:1px solid var(--border)">
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Name</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Permissions</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Rate Limit</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Status</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Expires</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tokens as $token)
                        <tr style="border-bottom:1px solid var(--border)" class="hover:bg-surface-2/50 transition">
                            <td class="px-4 py-3 font-medium" style="color:var(--text)">{{ $token->name }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($token->permissions ?? ['read'] as $perm)
                                        <span class="badge text-xs">{{ $perm }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3" style="color:var(--text-muted)">{{ $token->rate_limit ?? 60 }}/min</td>
                            <td class="px-4 py-3">
                                @if($token->revoked_at)
                                    <span class="badge" style="background:#fee2e2;color:#991b1b">Revoked</span>
                                @elseif($token->expires_at && $token->expires_at->isPast())
                                    <span class="badge" style="background:#fef3c7;color:#92400e">Expired</span>
                                @else
                                    <span class="badge" style="background:#dcfce7;color:#166534">Active</span>
                                @endif
                            </td>
                            <td class="px-4 py-3" style="color:var(--text-muted)">
                                {{ $token->expires_at ? $token->expires_at->format('M j, Y') : 'Never' }}
                            </td>
                            <td class="px-4 py-3">
                                @if(!$token->revoked_at)
                                    <form method="POST" action="{{ route('admin.api-tokens.revoke', $token) }}" style="display:inline" onsubmit="return confirm('Revoke this token?')">
                                        @csrf
                                        <button type="submit" class="text-sm font-medium hover:underline" style="color:#dc2626">Revoke</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($tokens->hasPages())
            <div class="p-4">{{ $tokens->links() }}</div>
        @endif
    @endif
</div>
@endsection
