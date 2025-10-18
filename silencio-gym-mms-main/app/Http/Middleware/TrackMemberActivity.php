<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

            // For now, just log the activity without updating database
            // This prevents errors when last_activity_at column doesn't exist
            Log::info('Member activity tracked', [
                'member_id' => $member->id,
                'email' => $member->email,
                'route' => $request->route()->getName(),
                'timestamp' => now()
            ]);
        }

        return $response;
    }
}
