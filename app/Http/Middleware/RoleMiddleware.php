<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Role;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        Log::info('RoleMiddleware handling request for route', ['route' => $request->getPathInfo()]);

        $user = $request->user()->load('role');

        Log::info('Checking user role', ['user_id' => $user->id ?? 'N/A']);

        if (!$user || !$user->role) {
            Log::warning('User not authenticated or role not found', ['user_id' => $user->id ?? 'N/A']);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

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
