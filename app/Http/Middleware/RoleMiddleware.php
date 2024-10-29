<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Role;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
{
    $user = $request->user();
    if (!$user || $user->role->name !== $role) {
        Log::info('User does not have the required role', [
            'user_id' => $user->id,
            'role_name' => $user->role->name ?? 'undefined',
            'expected_role' => $role
        ]);
        return response()->json(['error' => 'Forbidden'], 403);
    }
    return $next($request);
}
}
