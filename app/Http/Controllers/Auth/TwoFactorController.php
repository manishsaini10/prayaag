<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    public function showSetup()
    {
        $user = auth()->user();
        $secret = $user->two_factor_secret;
        $qrCodeUrl = null;

        if (!$secret) {
            $secret = \Illuminate\Support\Str::random(32);
            $user->update(['two_factor_secret' => $secret]);
        }

        $qrCodeUrl = sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s',
            urlencode(config('app.name')),
            urlencode($user->email),
            $secret,
            urlencode(config('app.name'))
        );

        return view('auth.two-factor-setup', compact('secret', 'qrCodeUrl'));
    }

    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = auth()->user();
        $secret = $user->two_factor_secret;

        // Simple TOTP verification for the setup step
        $valid = false;
        $timeSlice = floor(time() / 30);
        for ($i = -1; $i <= 1; $i++) {
            $generated = $this->generateTOTP($secret, $timeSlice + $i);
            if (hash_equals($generated, $request->code)) {
                $valid = true;
                break;
            }
        }

        if (!$valid) {
            return back()->withErrors(['code' => 'Invalid code. Please try again.']);
        }

        $user->update([
            'two_factor_enabled' => true,
            'two_factor_confirmed_at' => now(),
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Two-factor authentication enabled.');
    }

    public function disable()
    {
        $user = auth()->user();
        $user->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
            'two_factor_confirmed_at' => null,
        ]);

        return back()->with('success', 'Two-factor authentication disabled.');
    }

    public function showChallenge()
    {
        if (!auth()->user()?->two_factor_enabled) {
            return redirect()->route('admin.dashboard');
        }

        if (session('2fa_passed')) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.two-factor-challenge');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = auth()->user();
        $secret = $user->two_factor_secret;

        $valid = false;
        $timeSlice = floor(time() / 30);
        for ($i = -1; $i <= 1; $i++) {
            $generated = $this->generateTOTP($secret, $timeSlice + $i);
            if (hash_equals($generated, $request->code)) {
                $valid = true;
                break;
            }
        }

        if (!$valid) {
            return back()->withErrors(['code' => 'Invalid code. Please try again.']);
        }

        session(['2fa_passed' => true]);

        return redirect()->intended(route('admin.dashboard'));
    }

    private function generateTOTP(string $secret, int|float $timeSlice): string
    {
        $key = $secret;
        // Pad secret to at least 8 chars for HMAC
        while (strlen($key) < 8) {
            $key .= "\0";
        }
        $hash = hash_hmac('sha1', pack('N*', 0, 0, 0, $timeSlice), $key, true);
        $offset = ord($hash[19]) & 0xf;
        $code = (
                ((ord($hash[$offset]) & 0x7f) << 24) |
                ((ord($hash[$offset + 1]) & 0xff) << 16) |
                ((ord($hash[$offset + 2]) & 0xff) << 8) |
                (ord($hash[$offset + 3]) & 0xff)
            ) % 1000000;
        return str_pad((string) $code, 6, '0', STR_PAD_LEFT);
    }
}
