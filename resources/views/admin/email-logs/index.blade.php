@extends('admin.layout')

@section('title', 'Email Logs')
@section('subtitle', 'Full audit history of sent, queued, and failed email notifications')

@section('content')
    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 rounded-xl bg-rose-50 text-rose-800 border border-rose-200 text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    @if(($queuedCount ?? 0) > 0)
        <div class="mb-6 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white grid place-items-center font-bold">
                    ⏳
                </div>
                <div>
                    <h4 class="text-sm font-bold text-amber-900 dark:text-amber-200 m-0">{{ $queuedCount }} Emails Currently Queued</h4>
                    <p class="text-xs text-amber-700 dark:text-amber-400 m-0">These emails were waiting in the queue buffer. Click to deliver them now.</p>
                </div>
            </div>
            <form method="POST" action="{{ url('/admin/email-logs/flush-queue') }}" onsubmit="return confirm('Send all {{ $queuedCount }} queued emails immediately?')">
                @csrf
                <button type="submit" class="btn primary text-xs py-2 px-4 font-bold" style="background:#f59e0b;border:none">
                    ⚡ Send All {{ $queuedCount }} Queued Emails Now
                </button>
            </form>
        </div>
    @endif


    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm mb-6">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Status</label>
                <select name="status" class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 p-2.5 text-xs">
                    <option value="">All Statuses</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="queued" {{ request('status') === 'queued' ? 'selected' : '' }}>Queued</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Module</label>
                <select name="module" class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 p-2.5 text-xs">
                    <option value="">All Modules</option>
                    <option value="careers" {{ request('module') === 'careers' ? 'selected' : '' }}>Careers</option>
                    <option value="enquiry" {{ request('module') === 'enquiry' ? 'selected' : '' }}>Enquiry</option>
                    <option value="newsletter" {{ request('module') === 'newsletter' ? 'selected' : '' }}>Newsletter</option>
                    <option value="video_testimonials" {{ request('module') === 'video_testimonials' ? 'selected' : '' }}>Video Testimonials</option>
                    <option value="mess_menu" {{ request('module') === 'mess_menu' ? 'selected' : '' }}>Mess Menu</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Template or Subject..." class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 p-2.5 text-xs">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn primary py-2.5 px-4 text-xs">Filter</button>
                <a href="{{ route('admin.email-logs.index') }}" class="btn py-2.5 px-4 text-xs">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 text-[11px] uppercase font-bold text-slate-500 tracking-wider">
                        <th class="p-4">Template / Subject</th>
                        <th class="p-4">Module</th>
                        <th class="p-4">Recipient</th>
                        <th class="p-4">Provider</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Date</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-xs">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="p-4">
                                <div class="font-mono font-bold text-slate-900 dark:text-white">{{ $log->template_key }}</div>
                                <div class="text-slate-500 truncate max-w-xs">{{ $log->subject }}</div>
                            </td>
                            <td class="p-4 font-semibold text-slate-600 dark:text-slate-400 capitalize">
                                {{ $log->module ?: 'N/A' }}
                            </td>
                            <td class="p-4 font-mono text-slate-600 dark:text-slate-300">
                                {{ $log->to_address }}
                            </td>
                            <td class="p-4 font-mono text-slate-500 uppercase">
                                {{ $log->provider_used ?: '—' }}
                            </td>
                            <td class="p-4">
                                @if($log->status === 'sent')
                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">SENT</span>
                                @elseif($log->status === 'queued')
                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">QUEUED</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700" title="{{ $log->error_message }}">FAILED</span>
                                @endif
                            </td>
                            <td class="p-4 text-slate-500 text-[11px]">
                                {{ $log->created_at->format('M j, H:i') }}
                            </td>
                            <td class="p-4 text-right">
                                <form action="{{ route('admin.email-logs.resend', $log->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-[11px] transition-colors">
                                        Resend
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">No email logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection
