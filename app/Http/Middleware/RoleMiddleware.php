<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        Log::info('RoleMiddleware handling request for route', ['route' => $request->getPathInfo()]);

        /** @var User $user */
        $user = $request->user()->load('Role');

        Log::info('Checking user role', ['user_id' => $user->id ?? 'N/A']);

        if ($user->hasRole($role)) {
            Log::info('User has the required role, proceeding to controller');
            return $next($request);
        }

        Log::warning('User does not have the required role', [
            'user_id' => $user->id,
            'user_role' => $user->role->name,
            'expected_role' => $role,
        ]);

        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
