<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class EmployeeAuthController extends Controller
{
    /**
     * Show the employee login form
     */
    public function showLogin()
    {
        // If already logged in as employee, redirect to employee dashboard
        if (Auth::check() && Auth::user()->isEmployee()) {
            return redirect()->route('employee.dashboard');
        }
        
        // If logged in as admin, redirect to admin dashboard
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('dashboard');
        }
        
        // If logged in as member, redirect to member dashboard
        if (Auth::check() && Auth::user()->role === 'member') {
            return redirect()->route('member.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle employee login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Check if user is an employee
            if ($user->isEmployee()) {
                $request->session()->regenerate();
                return redirect()->route('employee.dashboard');
            }
            
            // If not an employee, logout and show error
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Access denied. Employee privileges required.',
            ]);
        }

        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Handle employee logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('employee.login');
    }
}
