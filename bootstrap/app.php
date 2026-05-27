<?php

use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\GuestMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
        $middleware->alias([
            'auth.custom' => AuthMiddleware::class,
            'guest' => GuestMiddleware::class,
            'active' => EnsureUserIsActive::class,
            'log.details' => \App\Http\Middleware\LogRequestDetails::class,
        ]);

        // Add Sanctum middleware for API
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
        // Handle unauthenticated API requests
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated. Please provide a valid token.'
                ], 401);
            }
        });
    })->create();
