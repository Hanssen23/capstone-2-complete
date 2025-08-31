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
        ]);
        
        // Apply error handler globally
        $middleware->web(prepend: [
            \App\Http\Middleware\ErrorHandler::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
