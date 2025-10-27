<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Member;


class AuthController extends Controller
{
    public function showLogin()
    {
        // MULTI-SESSION MODE: Always show login page
        // This allows testing with multiple user types in different browser tabs
        // Users can login with different accounts simultaneously

        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $email = $request->input('email');

        \Log::info('Login attempt', ['email' => $email]);

        // Check if the email exists in either users or members table
        $userExists = \App\Models\User::where('email', $email)->exists();
        $memberExists = \App\Models\Member::where('email', $email)->exists();

        \Log::info('User existence check', ['userExists' => $userExists, 'memberExists' => $memberExists]);

        // MULTI-SESSION MODE: Logout from the opposite guard before logging in
        // This prevents session conflicts when switching between user types

        // Attempt user login (admin/employee)
        if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            \Log::info('Web guard authentication successful');

            // Logout from member guard if logged in (to prevent conflicts)
            if (Auth::guard('member')->check()) {
                Auth::guard('member')->logout();
            }

            $user = Auth::guard('web')->user();

            // Check if account is activated
            if (!$user->email_verified_at) {
                \Log::info('Account not activated', ['email' => $email]);
                Auth::guard('web')->logout();
                return back()->withErrors([
                    'email' => 'Your account is not activated. Please contact an administrator.',
                ])->withInput($request->only('email'));
            }

            \Log::info('Login successful', ['email' => $email, 'role' => $user->role]);

            // Regenerate session for security
            $request->session()->regenerate();

            // Redirect based on user role
            if ($user->isAdmin()) {
                return redirect()->route('dashboard');
            } elseif ($user->isEmployee()) {
                return redirect()->route('employee.dashboard');
            } else {
                // Fallback for any other role
                return redirect()->route('dashboard');
            }
        }

        \Log::info('Web guard authentication failed');

        // Attempt member login
        if (Auth::guard('member')->attempt($credentials, $request->boolean('remember'))) {
            \Log::info('Member guard authentication successful');

            // Logout from web guard if logged in (to prevent conflicts)
            if (Auth::guard('web')->check()) {
                Auth::guard('web')->logout();
            }

            $member = Auth::guard('member')->user();

            // Check if email is verified
            if (!$member->hasVerifiedEmail()) {
                Auth::guard('member')->logout();
                return redirect()->route('member.verification.notice')->withErrors([
                    'email' => 'Please verify your email address before logging in.',
                ])->withInput($request->only('email'));
            }

            // Allow all verified members to login (no status check)
            // Members can login regardless of status (inactive, active, suspended, expired)
            // Only deleted members cannot login (handled by soft deletes)

            // Track login activity
            // TODO: Re-enable after adding last_login_at and last_activity_at columns to members table
            // $member->updateLastLogin();

            $request->session()->regenerate();
            return redirect()->intended('/member');
        }

        // Provide specific error messages
        if (!$userExists && !$memberExists) {
            return back()->withErrors([
                'email' => 'No account found with this email address. Please check your email or sign up for a new account.',
            ])->withInput($request->only('email'));
        } else {
            return back()->withErrors([
                'email' => 'The password you entered is incorrect. Please try again or reset your password.',
            ])->withInput($request->only('email'));
        }
    }

    public function logout(Request $request)
    {
        try {
            // Determine which guard is currently authenticated
            $isEmployee = Auth::guard('web')->check() && Auth::guard('web')->user()->isEmployee();

            // Logout from both guards if logged in
            if (Auth::guard('web')->check()) {
                Auth::guard('web')->logout();
            }
            if (Auth::guard('member')->check()) {
                Auth::guard('member')->logout();
            }

            // Invalidate session and regenerate token
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redirect to appropriate login page based on user type
            if ($isEmployee) {
                return redirect()->route('employee.auth.login.show')->with('success', 'You have been logged out successfully.');
            } else {
                // Default to main login page (for admin and members)
                return redirect()->route('login.show')->with('success', 'You have been logged out successfully.');
            }

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Logout error: ' . $e->getMessage());

            // Force logout even if there's an error
            Auth::logout();
            $request->session()->flush();

            // Default to main login page on error
            return redirect()->route('login.show')->with('error', 'Session expired. You have been logged out.');
        }
    }
} 