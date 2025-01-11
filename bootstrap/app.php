<?php

use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\VerifyCsrfToken; // Import the CSRF Token middleware
use Illuminate\Foundation\Application;
use Illuminate\Http\Middleware\HandleCors;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use App\Http\Middleware\LogCsrfTokens;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php', // Only API routes are being used
    )
    ->withMiddleware(function ($middleware) {
        // This is where middleware should be registered.
        // VerifyCsrfToken should only be applied to web routes but is now excluded for API routes.
        $middleware->statefulApi([
            EnsureFrontendRequestsAreStateful::class, // Handles frontend (React) requests that require cookies
            HandleCors::class, // CORS headers for cross-origin requests
            VerifyCsrfToken::class, // This line is redundant for API requests, since we excluded it in `VerifyCsrfToken.php`
        ]);
    })
    ->withExceptions(function ($exceptions) {
        //
    })->create();
