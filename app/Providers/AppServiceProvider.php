<?php

namespace App\Providers;

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app['router']->aliasMiddleware('role', RoleMiddleware::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Log::info('Request received', [
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'headers' => request()->headers->all(),
        ]);
            // Adding a preflight OPTIONS route for CORS
            Route::options('/{any}', function () {
                return response()->json([], 204);
            })->where('any', '.*');
    }

}
