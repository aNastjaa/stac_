<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminUserController extends Controller
{
    /**
     * Create a new user with a specific role.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:basic,pro,admin',
            'role_id' => 'sometimes|uuid|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Fetch the role ID either by direct `role_id` or by `role` name
        $roleId = $request->has('role_id')
            ? $request->input('role_id')
            : Role::where('name', $request->input('role'))->value('id');

        if (!$roleId) {
            return response()->json(['error' => 'Role not found.'], 404);
        }

        $user = User::create([
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'role_id' => $roleId,
        ]);

        return response()->json(['user' => $user], 201);
    }

    /**
     * Get all users with their role information.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = User::with('role')->get();
        return response()->json($users);
    }

    /**
     * Update a user's role.
     *
     * @param Request $request
     * @param string $userId
     * @return JsonResponse
     */
    public function updateRole(Request $request, string $userId): JsonResponse
    {
        // Validate the role in the request
        $validator = Validator::make($request->all(), [
            'role' => 'required|string|in:basic,pro,admin',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the user
        $user = User::findOrFail($userId);

        // Fetch the role by name
        $role = Role::where('name', $request->role)->first();

        if (!$role) {
            return response()->json(['error' => 'Role not found.'], 404);
        }

        // Update the user's role
        $user->role_id = $role->id;
        $user->save();

        // Return the updated user information
        return response()->json(['message' => 'User role updated successfully', 'user' => $user]);
    }

    /**
     * Delete a user.
     *
     * @param string $userId
     * @return JsonResponse
     */
    public function destroy(string $userId): JsonResponse
    {
        $user = User::findOrFail($userId);
        Admin::deleteUser($user);

        return response()->json(['message' => 'User deleted successfully.']);
    }
}
