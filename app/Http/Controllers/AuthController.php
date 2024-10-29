<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{

    // Register user
    // Register user
    public function register(Request $request)
    {
        $fields = $request->validate([
            'username' => 'required|string|min:3|max:16|regex:/^[\S]+$/|unique:users',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            //Password rules!!
        ]);

        $basicRoleId = DB::table('roles')->where('name', 'basic')->value('id');
        $fields['role_id'] = $basicRoleId;

        // Create the user
        $user = User::create($fields);

        // Create the token for the user
        $token = $user->createToken($request->username)->plainTextToken;

        Log::info('New User Created:', ['user' => $user->toArray()]);

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    // Login user
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }

        $token = $user->createToken($user->username)->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'redirect_url' => '/api/users/profile',
        ], 200);
    }

    // Logout
    public function logout(Request $request) {
        // Invalidate all user tokens
        $request->user()->tokens()->delete();

        // Log out the user from the session if any
        Auth::logout();

        return response()->json(['message' => 'You are logged out.']);
    }

}
