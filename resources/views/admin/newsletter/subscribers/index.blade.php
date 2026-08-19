@extends('admin.layout')

@section('title', 'Newsletter Subscribers')
@section('subtitle', 'Manage parent newsletter opt-in list')

@section('actions')
    <a href="{{ route('admin.newsletter.subscribers.export') }}" class="btn">
        <x-admin.icon name="arrow-down-tray"/> Export CSV
    </a>
@endsection

@section('content')
    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 text-[11px] uppercase font-bold text-slate-500 tracking-wider">
                    <th class="p-4">Email</th>
                    <th class="p-4">Name</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Consent Source</th>
                    <th class="p-4">Subscribed At</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-xs">
                @forelse($subscribers as $s)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="p-4 font-mono font-bold text-slate-900 dark:text-white">
                            {{ $s->email }}
                        </td>
                        <td class="p-4 text-slate-600 dark:text-slate-300">
                            {{ $s->name ?: '—' }}
                        </td>
                        <td class="p-4">
                            @if($s->status === 'subscribed')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">SUBSCRIBED</span>
                            @elseif($s->status === 'pending')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">PENDING CONFIRMATION</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">UNSUBSCRIBED</span>
                            @endif
                        </td>
                        <td class="p-4 font-mono text-[11px] text-slate-500">
                            {{ $s->consent_source ?: 'website' }}
                        </td>
                        <td class="p-4 text-slate-500 text-[11px]">
                            {{ $s->subscribed_at ? $s->subscribed_at->format('M j, Y') : 'Pending' }}
                        </td>
                        <td class="p-4 text-right">
                            @if($s->status === 'subscribed')
                                <form action="{{ route('admin.newsletter.subscribers.unsubscribe', $s->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 text-[11px] font-semibold transition-colors">
                                        Unsubscribe
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400">No subscribers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($subscribers->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                {{ $subscribers->links() }}
            </div>
        @endif
    </div>
@endsection
