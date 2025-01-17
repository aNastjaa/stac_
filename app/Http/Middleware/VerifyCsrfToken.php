<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Log;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Add paths to exclude from CSRF verification if needed
        'api/auth/register',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, \Closure $next)
    {
        Log::info('CSRF Middleware Triggered', [
            'csrf_token_header' => $request->header('X-XSRF-TOKEN'),
            'csrf_token_cookie' => $request->cookie('XSRF-TOKEN'),
        ]);

        // This is the correct usage since the class extends the parent class
        return parent::handle($request, $next);
    }

    /**
     * Check if the CSRF tokens match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        Log::info('CSRF Token Match Check', [
            'request_token' => $request->header('X-XSRF-TOKEN'),
            'cookie_token' => $request->cookie('XSRF-TOKEN'),
        ]);

        // Use the parent method for token comparison
        return parent::tokensMatch($request);
    }
}
