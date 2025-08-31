<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        // Check for admin credentials
        if ($credentials['email'] === 'admin@admin.com' && $credentials['password'] === 'admin123') {
            // Create or find admin user
            $admin = User::firstOrCreate(
                ['email' => 'admin@admin.com'],
                [
                    'name' => 'Admin',
                    'password' => Hash::make('admin123'),
                ]
            );

            Auth::login($admin);

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
} 