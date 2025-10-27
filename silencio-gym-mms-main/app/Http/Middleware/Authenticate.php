<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // For API requests, return null to send 401 response instead of redirect
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }

        // Prevent redirect loop by checking if we're already on login page
        if ($request->is('login') || $request->is('/')) {
            return null;
        }

        // Check if this is a member route - redirect to member login
        if ($request->is('member') || $request->is('member/*')) {
            return route('login.show');
        }

        return route('login.show');
    }

    /**
     * Handle an unauthenticated user.
     */
    protected function unauthenticated($request, array $guards)
    {
        // For API requests, throw an authentication exception
        if ($request->expectsJson() || $request->is('api/*')) {
            throw new \Illuminate\Auth\AuthenticationException(
                'Unauthenticated.', $guards, $this->redirectTo($request)
            );
        }

        parent::unauthenticated($request, $guards);
    }
}