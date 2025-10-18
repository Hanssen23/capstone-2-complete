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
        // Only redirect if user is already authenticated and not already on login page
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            if ($user->isAdmin()) {
                return redirect()->route('dashboard');
            } elseif ($user->isEmployee()) {
                return redirect()->route('employee.dashboard');
            }
        }
        
        if (Auth::guard('member')->check()) {
            return redirect()->route('member.dashboard');
        }

        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        // Attempt user login (admin/employee)
        if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard('web')->user();
            
            // Check if account is activated
            if (!$user->email_verified_at) {
                Auth::guard('web')->logout();
                return back()->withErrors([
                    'email' => 'Your account is not activated. Please contact an administrator.',
                ])->withInput($request->only('email'));
            }
            
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

        // Attempt member login
        if (Auth::guard('member')->attempt($credentials, $request->boolean('remember'))) {
            $member = Auth::guard('member')->user();

            // Check if email is verified
            if (!$member->hasVerifiedEmail()) {
                Auth::guard('member')->logout();
                return redirect()->route('member.verification.notice')->withErrors([
                    'email' => 'Please verify your email address before logging in.',
                ])->withInput($request->only('email'));
            }

            // Check if account is active
            if ($member->status !== 'active') {
                Auth::guard('member')->logout();
                return back()->withErrors([
                    'email' => 'Your account is not active. Please contact the gym administrator.',
                ])->withInput($request->only('email'));
            }

            // Track login activity
            $member->updateLastLogin();

            $request->session()->regenerate();
            return redirect()->intended('/member');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
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