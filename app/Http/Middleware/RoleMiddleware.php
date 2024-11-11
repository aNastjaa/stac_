<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        Log::info('RoleMiddleware handling request for route', ['route' => $request->getPathInfo()]);

        /** @var User $user */
        $user = $request->user();

        // Check if the user is authenticated
        if (!$user) {
            Log::warning('Unauthorized access attempt, user not authenticated');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Eager load the role relationship to avoid N+1 problem
        $user->load('role');

        Log::info('Checking user role', ['user_id' => $user->id]);

        // Check if the user has the required role
        if ($user->hasRole($role)) {
            Log::info('User has the required role, proceeding to controller');
            return $next($request);
        }

        // Log when the user doesn't have the required role
        Log::warning('User does not have the required role', [
            'user_id' => $user->id,
            'user_role' => $user->role->name ?? 'N/A',
            'expected_role' => $role,
        ]);

        return response()->json(['error' => 'Forbidden'], 403);
    }
}
