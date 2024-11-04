<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    // // Create a new admin user
    // public function create(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:admin_users',
    //         'password' => 'required|string|min:8',
    //     ]);

    //     $adminUser = AdminUser::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);

    //     return response()->json($adminUser, 201);
    // }

    // // Get all admin users
    // public function index()
    // {
    //     return AdminUser::all();
    // }

    // // Update user role
    // public function updateRole(Request $request, $id)
    // {
    //     $user = AdminUser::findOrFail($id);
    //     // Update user role logic here if applicable
    //     return response()->json($user, 200);
    // }

    // // Delete user
    // public function destroy($id)
    // {
    //     $user = AdminUser::findOrFail($id);
    //     $user->delete();
    //     return response()->json(null, 204);
    // }
}
