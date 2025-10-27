<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class GuardSessionManager
{
    /**
     * Handle an incoming request.
     * 
     * This middleware enables multiple simultaneous sessions by using
     * different session cookies for different authentication guards.
     * This allows testing with multiple user types (admin, employee, member)
     * logged in at the same time in different browser tabs.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $guard = null): Response
    {
        // If no guard specified, try to determine from route or use default
        if (!$guard) {
            $guard = $this->determineGuardFromRequest($request);
        }

        // Set the session cookie name based on the guard
        $this->setGuardSessionCookie($guard);

        $response = $next($request);

        // Ensure the response uses the correct session cookie for this guard
        $this->ensureGuardCookie($response, $guard);

        return $response;
    }

    /**
     * Determine which guard to use based on the request
     */
    protected function determineGuardFromRequest(Request $request): string
    {
        // Check if user is trying to access member routes
        if ($request->is('member') || $request->is('member/*')) {
            return 'member';
        }

        // Check if member is already authenticated
        if (Auth::guard('member')->check()) {
            return 'member';
        }

        // Check if web user is authenticated
        if (Auth::guard('web')->check()) {
            return 'web';
        }

        // Default to web guard for login page and admin/employee routes
        return 'web';
    }

    /**
     * Set the session cookie name for the specified guard
     */
    protected function setGuardSessionCookie(string $guard): void
    {
        $guardCookies = config('session.guard_cookies', []);
        
        if (isset($guardCookies[$guard])) {
            // Temporarily override the session cookie name for this request
            Config::set('session.cookie', $guardCookies[$guard]);
        }
    }

    /**
     * Ensure the response has the correct session cookie for the guard
     */
    protected function ensureGuardCookie(Response $response, string $guard): void
    {
        $guardCookies = config('session.guard_cookies', []);
        
        if (isset($guardCookies[$guard])) {
            // The session cookie should already be set correctly by Laravel's session middleware
            // This method is here for any additional cookie handling if needed in the future
        }
    }
}

