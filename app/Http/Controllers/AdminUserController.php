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
    public function create(Request $request)
{
    $validator = Validator::make($request->all(), [
        'username' => 'required|string|max:255|unique:users',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'role' => 'sometimes|string|in:basic,pro,admin',
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

    public function index(): JsonResponse
    {
        $users = User::with('role')->get();
        return response()->json($users);
    }

    public function updateRole(Request $request, $id): JsonResponse
{
    $validator = Validator::make($request->all(), [
        'role' => 'required|string|in:basic,pro,admin',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $user = User::findOrFail($id);

    // Fetch the role ID based on the role name from the request
    $role = Role::where('name', $request->role)->first();

    if (!$role) {
        return response()->json(['error' => 'Role not found.'], 404);
    }

    // Update the user's role_id directly
    $user->role_id = $role->id;
    $user->save();

    return response()->json(['message' => 'User role updated successfully', 'user' => $user]);
}

    public function destroy($id): JsonResponse
    {
        $user = User::findOrFail($id);
        Admin::deleteUser($user);

        return response()->json(['message' => 'User deleted successfully.']);
    }
}
