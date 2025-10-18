<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackMemberActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track activity for authenticated members
        if (Auth::guard('member')->check()) {
            $member = Auth::guard('member')->user();
            
            // Update last activity timestamp
            // Use a simple update to avoid triggering model events
            $member->timestamps = false; // Prevent updated_at from changing
            $member->update(['last_activity_at' => now()]);
            $member->timestamps = true; // Re-enable timestamps
        }

        return $response;
    }
}
