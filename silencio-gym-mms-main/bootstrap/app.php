<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
        ]);
        
        // Use our custom CSRF middleware with exceptions
        $middleware->web(append: [
            \App\Http\Middleware\VerifyCsrfToken::class,
        ]);
        
        // Add RFID reader middleware to authenticated routes
        $middleware->alias([
            'start.rfid' => \App\Http\Middleware\StartRfidReader::class,
            'error.handler' => \App\Http\Middleware\ErrorHandler::class,
            'admin.only' => \App\Http\Middleware\AdminOnly::class,
            'employee.only' => \App\Http\Middleware\EmployeeOnly::class,
            'member.only' => \App\Http\Middleware\MemberOnly::class,
            'prevent.back' => \App\Http\Middleware\PreventBackHistory::class,
            'prevent.member.admin' => \App\Http\Middleware\PreventMemberAdminAccess::class,
            'ensure.session' => \App\Http\Middleware\EnsureSessionPersistence::class,
        ]);
        
        // Apply error handler globally
        $middleware->web(prepend: [
            \App\Http\Middleware\ErrorHandler::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
