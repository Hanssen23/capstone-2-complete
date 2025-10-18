<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberEmailVerificationController extends Controller
{
    /**
     * Display the email verification notice.
     */
    public function notice()
    {
        return view('auth.member-verify-email');
    }

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(Request $request)
    {
        $member = Member::findOrFail($request->route('id'));

        if (! hash_equals((string) $request->route('hash'), sha1($member->getEmailForVerification()))) {
            return redirect()->route('member.verification.notice')->with('error', 'Invalid verification link.');
        }

        if ($member->hasVerifiedEmail()) {
            return redirect()->route('login.show')->with('status', 'Email already verified. You can now log in.');
        }

        if ($member->markEmailAsVerified()) {
            // Activate the member account after email verification
            $member->update(['status' => 'active']);
            event(new Verified($member));
        }

        return redirect()->route('login.show')->with('status', 'Email verified successfully! You can now log in.');
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $member = Member::where('email', $request->email)->first();

        if (!$member) {
            return back()->withErrors(['email' => 'No account found with this email address.']);
        }

        if ($member->hasVerifiedEmail()) {
            return back()->with('status', 'Email is already verified.');
        }

        $member->sendEmailVerificationNotification();

        return back()->with('status', 'Verification link sent!');
    }
}
