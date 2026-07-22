<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    public function showSetup()
    {
        $user = auth()->user();
        $secret = $user->two_factor_secret;
        $qrCodeUrl = null;

        if (!$secret) {
            $secret = strtoupper(Str::random(16));
            $user->update(['two_factor_secret' => $secret]);
        }

        $qrCodeUrl = sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s',
            urlencode(config('app.name')),
            urlencode($user->email),
            $secret,
            urlencode(config('app.name'))
        );

        $recoveryCodes = [];
        if ($user->two_factor_recovery_codes) {
            try {
                $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true) ?: [];
            } catch (\Exception $e) {
                $recoveryCodes = [];
            }
        }

        return view('auth.two-factor-setup', compact('secret', 'qrCodeUrl', 'recoveryCodes'));
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

        // Generate 8 recovery codes
        $recoveryCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $recoveryCodes[] = sprintf(
                '%04x-%04x',
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff)
            );
        }

        $user->update([
            'two_factor_enabled' => true,
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
        ]);

        // Keep 2fa_passed active for current session once set up successfully
        session(['2fa_passed' => true]);

        return redirect()->route('admin.dashboard')->with('success', 'Two-factor authentication enabled successfully. Please record your recovery codes below!');
    }

    public function disable()
    {
        $user = auth()->user();
        $user->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => null,
        ]);

        session()->forget('2fa_passed');

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
            'code' => 'required|string',
        ]);

        $user = auth()->user();
        $code = $request->code;

        // 1. Check recovery codes if matches format (e.g. contains hyphen or length > 6)
        if (strlen($code) > 6 && str_contains($code, '-')) {
            $storedCodes = [];
            if ($user->two_factor_recovery_codes) {
                try {
                    $storedCodes = json_decode(decrypt($user->two_factor_recovery_codes), true) ?: [];
                } catch (\Exception $e) {
                    $storedCodes = [];
                }
            }

            if (($key = array_search($code, $storedCodes)) !== false) {
                unset($storedCodes[$key]);
                $user->update([
                    'two_factor_recovery_codes' => encrypt(json_encode(array_values($storedCodes))),
                ]);

                session(['2fa_passed' => true]);
                return redirect()->intended(route('admin.dashboard'));
            }

            return back()->withErrors(['code' => 'Invalid recovery code. Please try again.']);
        }

        // 2. Validate standard 6-digit TOTP code
        $secret = $user->two_factor_secret;
        $valid = false;
        $timeSlice = floor(time() / 30);
        for ($i = -1; $i <= 1; $i++) {
            $generated = $this->generateTOTP($secret, $timeSlice + $i);
            if (hash_equals($generated, $code)) {
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
