@extends('admin.layout')
@section('title', 'Two-Factor Authentication')
@php
    // Override sidebar for challenge page
    $hideSidebar = true;
@endphp
@section('content')
<div class="max-w-md mx-auto mt-16">
    <div class="card p-8 text-center">
        <div class="w-16 h-16 rounded-2xl mx-auto mb-4 grid place-items-center" style="background:var(--surface-2)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:32px;height:32px;color:var(--primary)">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold mb-2" style="color:var(--text)">Two-Factor Authentication</h2>

        @if($errors->any())
            <div class="mb-4 px-4 py-3 rounded-xl text-sm" style="background:#fee2e2;color:#991b1b">
                {{ $errors->first() }}
            </div>
        @endif

        <div x-data="{ useRecovery: false }">
            <p class="text-sm mb-6" style="color:var(--text-muted)" x-text="useRecovery ? 'Enter an emergency recovery code to log in.' : 'Enter the authentication code from your authenticator app.'"></p>

            <form method="POST" action="{{ route('2fa.verify') }}" class="space-y-4">
                @csrf
                <div>
                    {{-- TOTP Input --}}
                    <input x-show="!useRecovery" type="text" name="code_totp" placeholder="000000" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autocomplete="one-time-code" class="w-full text-center text-3xl tracking-[0.5em] focus:outline-none" style="max-width:280px;margin:0 auto;font-weight:700" :disabled="useRecovery">

                    {{-- Recovery Code Input --}}
                    <input x-show="useRecovery" type="text" name="code_recovery" placeholder="xxxx-xxxx" maxlength="12" class="w-full text-center text-lg tracking-wider focus:outline-none border p-3 rounded-lg" style="max-width:280px;margin:0 auto;border-color:var(--border);background:var(--surface-2);color:var(--text)" :disabled="!useRecovery">

                    <input type="hidden" name="code" :value="useRecovery ? $el.form.code_recovery.value : $el.form.code_totp.value">
                </div>
                <button type="submit" class="btn-primary w-full" @click="$el.form.code.value = useRecovery ? $el.form.code_recovery.value : $el.form.code_totp.value">Verify</button>
            </form>

            <div class="mt-4">
                <button type="button" class="text-xs font-semibold hover:underline" style="color:var(--primary)" @click="useRecovery = !useRecovery" x-text="useRecovery ? 'Use authenticator application' : 'Use emergency recovery code'"></button>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" class="text-sm font-medium hover:underline" style="color:var(--text-muted)">Sign out</button>
        </form>
    </div>
</div>
@endsection
