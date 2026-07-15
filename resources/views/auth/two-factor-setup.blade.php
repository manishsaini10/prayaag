@extends('admin.layout')
@section('title', 'Two-Factor Authentication')
@section('content')
<div class="max-w-lg mx-auto mt-8">
    <div class="card p-8 text-center">
        <h2 class="text-xl font-bold mb-4" style="color:var(--text)">Set Up Two-Factor Authentication</h2>

        @if(auth()->user()->two_factor_enabled)
            <div class="mb-6 px-4 py-3 rounded-xl text-sm" style="background:#dcfce7;color:#166534">
                2FA is currently enabled.
            </div>
            <form method="POST" action="{{ route('2fa.disable') }}" onsubmit="return confirm('Disable 2FA?')">
                @csrf
                <button type="submit" class="btn-secondary" style="color:#dc2626">Disable 2FA</button>
            </form>
        @else
            <p class="text-sm mb-6" style="color:var(--text-muted)">
                Scan this QR code with your authenticator app (Google Authenticator, Authy, etc.),
                then enter the 6-digit code below to confirm.
            </p>

            <div class="mb-6 inline-block p-4 rounded-xl" style="background:#fff;border:2px dashed var(--border)">
                <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl={{ urlencode($qrCodeUrl) }}&choe=UTF-8" alt="QR Code" width="200" height="200" style="border-radius:8px">
            </div>

            <div class="mb-4 font-mono text-sm p-3 rounded-lg break-all" style="background:var(--surface-2);color:var(--text-muted)">
                Secret: <strong>{{ $secret }}</strong>
            </div>

            @if($errors->any())
                <div class="mb-4 px-4 py-3 rounded-xl text-sm" style="background:#fee2e2;color:#991b1b">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('2fa.enable') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Authenticator Code</label>
                    <input type="text" name="code" placeholder="000000" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autocomplete="one-time-code" class="w-full text-center text-2xl tracking-widest" style="max-width:200px;margin:0 auto">
                </div>
                <button type="submit" class="btn-primary">Verify & Enable 2FA</button>
            </form>
        @endif
    </div>
</div>
@endsection
