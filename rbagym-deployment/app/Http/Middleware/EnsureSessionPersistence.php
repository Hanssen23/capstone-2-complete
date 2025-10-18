<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class EnsureSessionPersistence
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Only apply to authenticated users
        if (Auth::check()) {
            $user = Auth::user();
            
            // Ensure session data is properly maintained
            if (!$request->session()->has('user_id') || $request->session()->get('user_id') !== $user->id) {
                $request->session()->put('user_id', $user->id);
                $request->session()->put('user_role', $user->role);
                $request->session()->put('user_email', $user->email);
            }
            
            // Extend session lifetime for active users (especially employees)
            $lastActivity = $request->session()->get('last_activity', time());
            $currentTime = time();
            
            // If user is active (within last 30 minutes), extend session
            if (($currentTime - $lastActivity) < 1800) { // 30 minutes
                $request->session()->put('last_activity', $currentTime);
                
                // For employees, extend session more aggressively
                if ($user->isEmployee()) {
                    $request->session()->put('employee_active', true);
                    $request->session()->put('session_extended', $currentTime);
                }
            }
        }

        $response = $next($request);

        // Ensure session is saved and CSRF token is refreshed for AJAX requests
        if (Auth::check()) {
            $request->session()->save();
            
            // Refresh CSRF token for AJAX requests to prevent token mismatch
            if ($request->ajax() || $request->wantsJson()) {
                $response->headers->set('X-CSRF-TOKEN', csrf_token());
            }
        }

        return $response;
    }
}
