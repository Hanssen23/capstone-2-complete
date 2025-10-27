<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MemberOnly
{
    public function handle(Request $request, Closure $next)
    {
        // Debug logging
        Log::info('MemberOnly middleware check', [
            'url' => $request->url(),
            'member_check' => Auth::guard('member')->check(),
            'web_check' => Auth::guard('web')->check(),
            'session_id' => $request->session()->getId(),
            'has_session' => $request->hasSession(),
        ]);

        // Check if user is authenticated via member guard
        if (!Auth::guard('member')->check()) {
            Log::warning('Member guard check failed', [
                'url' => $request->url(),
                'session_data' => $request->session()->all(),
            ]);

            if (Auth::guard('web')->check()) {
                $user = Auth::guard('web')->user();
                if ($user->isAdmin()) {
                    return redirect()->route('dashboard');
                } elseif ($user->isEmployee()) {
                    return redirect()->route('employee.dashboard');
                } else {
                    return redirect()->route('dashboard');
                }
            }
            // Prevent redirect loop by checking if we're already on login page
            if (!$request->is('login') && !$request->is('/')) {
                return redirect()->route('login.show');
            }
            return redirect()->route('login.show');
        }

        // Additional security: Verify the member has member role
        $member = Auth::guard('member')->user();
        if (!$member || !$member->isMember()) {
            Log::error('Member role verification failed', [
                'member_id' => $member?->id,
                'member_role' => $member?->role,
            ]);

            // If somehow a non-member user is logged in via member guard, logout and redirect
            Auth::guard('member')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login.show')->withErrors([
                'email' => 'Access denied. Member privileges required.'
            ]);
        }

        Log::info('MemberOnly middleware passed', [
            'member_id' => $member->id,
            'member_email' => $member->email,
        ]);

        return $next($request);
    }
}


