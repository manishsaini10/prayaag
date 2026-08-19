@extends('admin.layout')

@section('title', 'Edit Email Provider')
@section('subtitle', 'Update credentials for ' . $provider->label)

@section('content')
<div class="max-w-3xl mx-auto bg-slate-900/90 rounded-2xl p-8 border border-slate-700/80 shadow-md">
    <form action="{{ route('admin.email-providers.update', $provider->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-semibold text-white mb-2">Provider Type</label>
            <input type="text" disabled value="{{ strtoupper($provider->provider_key) }}" class="w-full rounded-xl border-slate-700 bg-slate-800 p-3 text-sm font-mono text-slate-400">
        </div>

        <div>
            <label class="block text-sm font-semibold text-white mb-2">Provider Label / Name</label>
            <input type="text" name="label" value="{{ old('label', $provider->label) }}" required class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-3 text-sm">
        </div>

        <div class="p-4 rounded-xl bg-amber-950/80 text-amber-200 border border-amber-800 text-xs">
            <strong>Security Note:</strong> Passwords and API keys are encrypted. Leave password/API key fields blank if you do not wish to update them.
        </div>

        <div class="space-y-4 pt-4 border-t border-slate-800">
            @foreach($provider->credentials ?? [] as $key => $val)
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">{{ str_replace('_', ' ', $key) }}</label>
                    @if(in_array($key, ['password', 'api_key', 'secret_key']))
                        <input type="password" name="credentials[{{ $key }}]" placeholder="•••••••• (leave blank to keep current)" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                    @else
                        <input type="text" name="credentials[{{ $key }}]" value="{{ $val }}" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-2.5 text-sm">
                    @endif
                </div>
            @endforeach
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-800">
            <a href="{{ route('admin.email-providers.index') }}" class="btn">Cancel</a>
            <button type="submit" class="btn primary">Update Provider</button>
        </div>
    </form>
</div>
@endsection
