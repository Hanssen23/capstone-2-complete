<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreventMemberAdminAccess
{
    /**
     * Handle an incoming request.
     * This middleware prevents members from accessing admin routes
     * even if they try to manually navigate to admin URLs.
     */
    public function handle(Request $request, Closure $next)
    {
        // If a member is logged in and trying to access admin routes, redirect them
        if (Auth::guard('member')->check()) {
            $member = Auth::guard('member')->user();
            
            // Double-check the member has member role
            if ($member && $member->isMember()) {
                return redirect()->route('member.dashboard')->withErrors([
                    'access' => 'Access denied. You do not have permission to access admin features.'
                ]);
            }
        }

        return $next($request);
    }
}
