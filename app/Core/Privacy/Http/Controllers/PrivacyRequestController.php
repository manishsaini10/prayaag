<?php

namespace App\Core\Privacy\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Core\Privacy\Models\DataPrivacyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PrivacyRequestController extends Controller
{
    public function showForm()
    {
        return view('privacy.request-my-data');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'email'        => 'required|email|max:255',
            'request_type' => 'required|in:export,delete',
        ]);

        $token = Str::random(64);

        DataPrivacyRequest::create([
            'email'              => $request->email,
            'request_type'       => $request->request_type,
            'status'             => 'pending',
            'verification_token' => $token,
            'ip_address'         => $request->ip(),
        ]);

        $verificationLink = route('privacy.verify', ['token' => $token]);

        rescue(function () use ($request, $verificationLink) {
            Mail::raw(
                "Verify your data privacy request by clicking the link below:\n\n{$verificationLink}\n\nThis link will expire soon.",
                function ($message) use ($request) {
                    $message->to($request->email)
                        ->subject('Verify your Data Privacy Request');
                }
            );
        }, null, false);

        return back()->with('success', 'Verification link has been sent to your email. Please verify to process.');
    }

    public function verify(string $token)
    {
        $privacyRequest = DataPrivacyRequest::where('verification_token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        $privacyRequest->update([
            'status'      => 'verified',
            'verified_at' => now(),
        ]);

        return redirect()->route('privacy.form')->with('success', 'Email verified successfully! Our administration will process your request shortly.');
    }
}
