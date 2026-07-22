@extends('admin.layout')

@section('title', 'Security Audit Trail')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold tracking-tight" style="color:var(--text)">Security Audit Trail</h2>
            <p class="text-xs" style="color:var(--text-muted)">Trace admin logins, configurations, data edits, and access histories.</p>
        </div>
        
        <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-sm flex items-center gap-1.5 px-3 py-1.5 border rounded-lg text-sm transition-colors font-medium" style="border-color:var(--border); background:var(--card-bg); color:var(--text)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v12m0 0l-3-3m3 3l3-3m-9 7h12"/></svg>
            <span>Export CSV</span>
        </a>
    </div>

    {{-- Filters Card --}}
    <form method="GET" action="{{ url('/admin/audit') }}" class="card p-4 border rounded-xl" style="border-color:var(--border)">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:var(--text-muted)">User (Causer)</label>
                <select name="user_id" class="w-full text-xs rounded-lg border px-3 py-2" style="background:var(--card-bg); border-color:var(--border); color:var(--text)">
                    <option value="">All Users</option>
                    @foreach($admins as $admin)
                        <option value="{{ $admin->id }}" {{ request('user_id') === $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:var(--text-muted)">Module</label>
                <select name="log_name" class="w-full text-xs rounded-lg border px-3 py-2" style="background:var(--card-bg); border-color:var(--border); color:var(--text)">
                    <option value="">All Modules</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}" {{ request('log_name') === $mod ? 'selected' : '' }}>{{ ucfirst($mod) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:var(--text-muted)">From Date</label>
                <input type="date" name="from" value="{{ request('from') }}" class="w-full text-xs rounded-lg border px-3 py-1.5" style="background:var(--card-bg); border-color:var(--border); color:var(--text)" />
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:var(--text-muted)">To Date</label>
                <input type="date" name="to" value="{{ request('to') }}" class="w-full text-xs rounded-lg border px-3 py-1.5" style="background:var(--card-bg); border-color:var(--border); color:var(--text)" />
            </div>

            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="block text-xs font-semibold mb-1.5" style="color:var(--text-muted)">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search action..." class="w-full text-xs rounded-lg border px-3 py-2" style="background:var(--card-bg); border-color:var(--border); color:var(--text)" />
                </div>
                <button type="submit" class="btn btn-primary py-2 px-4 rounded-lg text-xs font-semibold">Filter</button>
            </div>
        </div>
    </form>

    {{-- Audit Log Table --}}
    <div class="card border rounded-xl overflow-hidden" style="border-color:var(--border)">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b" style="border-color:var(--border); color:var(--text-muted)">
                        <th class="py-3.5 px-4 font-semibold">Module</th>
                        <th class="py-3.5 font-semibold">Description</th>
                        <th class="py-3.5 font-semibold">Causer ID</th>
                        <th class="py-3.5 font-semibold">Date Time</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:var(--border)">
                    @forelse($logs as $log)
                        <tr style="color:var(--text)">
                            <td class="py-3 px-4 font-mono text-xs uppercase">{{ $log->log_name }}</td>
                            <td class="py-3 font-medium">{{ $log->description }}</td>
                            <td class="py-3 font-mono text-xs">{{ $log->causer_id ?? 'System' }}</td>
                            <td class="py-3 text-xs">{{ $log->created_at->format('M j, Y H:i:s') }}</td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.audit.show', $log->id) }}" class="text-xs font-semibold hover:underline" style="color:var(--primary)">
                                    View Diff
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8" style="color:var(--text-muted)">No audit trail activities found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t" style="border-color:var(--border)">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
