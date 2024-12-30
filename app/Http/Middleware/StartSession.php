<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Session\Middleware\StartSession as Middleware;

class StartSession extends Middleware
{
    public function handle($request, Closure $next)
    {
        // Your CSRF verification logic here
        return parent::handle($request, $next);
    }

}
