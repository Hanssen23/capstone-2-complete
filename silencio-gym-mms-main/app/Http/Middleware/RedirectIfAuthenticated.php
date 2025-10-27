<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * MULTI-SESSION MODE: This middleware is now permissive to allow
     * multiple user types to be logged in simultaneously in different tabs.
     * This is useful for testing purposes.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        // MULTI-SESSION SUPPORT: Allow access to login page even if authenticated
        // This enables testing with multiple accounts in different tabs

        // Check if this is a login page request
        $isLoginPage = $request->is('login') || $request->is('register');

        // If accessing login/register page, allow it (for multi-session testing)
        if ($isLoginPage) {
            return $next($request);
        }

        // For other routes, apply normal redirect logic
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // Redirect based on user type
                if ($guard === 'member') {
                    return redirect()->route('member.dashboard');
                } elseif ($user->isAdmin()) {
                    return redirect()->route('dashboard');
                } elseif ($user->isEmployee()) {
                    return redirect()->route('employee.dashboard');
                } else {
                    return redirect()->route('dashboard');
                }
            }
        }

        return $next($request);
    }
}
