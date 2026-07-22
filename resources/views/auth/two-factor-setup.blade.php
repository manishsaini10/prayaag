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

            @if(count($recoveryCodes) > 0)
                <div class="card p-5 text-left mb-6" style="border:1px solid var(--border)">
                    <h3 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:var(--text-muted)">Your Active Recovery Codes</h3>
                    <p class="text-xs mb-4" style="color:var(--text-muted)">Save these codes. Each code can only be used once for emergency verification login.</p>
                    <div class="grid grid-cols-2 gap-2 font-mono text-sm" style="color:var(--text)">
                        @foreach($recoveryCodes as $code)
                            <div class="p-2 rounded border text-center" style="border-color:var(--border); background:var(--surface-2)">{{ $code }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

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
                <p style="color:var(--text-muted); font-size:0.8rem;">QR URL: {{ $qrCodeUrl }}</p>
                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($qrCodeUrl) !!}
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
