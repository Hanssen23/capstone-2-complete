<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'guard.session' => \App\Http\Middleware\GuardSessionManager::class,
            'start.rfid' => \App\Http\Middleware\StartRfidReader::class,
            'error.handler' => \App\Http\Middleware\ErrorHandler::class,
            'admin.only' => \App\Http\Middleware\AdminOnly::class,
            'employee.only' => \App\Http\Middleware\EmployeeOnly::class,
            'member.only' => \App\Http\Middleware\MemberOnly::class,
            'prevent.back' => \App\Http\Middleware\PreventBackHistory::class,
            'prevent.member.admin' => \App\Http\Middleware\PreventMemberAdminAccess::class,
            'ensure.session' => \App\Http\Middleware\EnsureSessionPersistence::class,
            'track.member.activity' => \App\Http\Middleware\TrackMemberActivity::class,
        ]);

        // TEMPORARILY DISABLED: Guard session manager causing 419 CSRF errors
        // The middleware was changing session cookie names after session started,
        // causing CSRF token mismatches. Need to refactor this approach.
        // $middleware->web(append: [
        //     \App\Http\Middleware\GuardSessionManager::class,
        // ]);

        // Add session middleware to API routes for authentication
        $middleware->api(prepend: [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        ]);

        // Configure authentication redirects
        $middleware->redirectGuestsTo(fn () => route('login.show'));
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'rfid/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
