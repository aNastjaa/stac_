<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{

    // Register user
    public function register(Request $request)
    {
        // Validate input fields
        $fields = $request->validate([
            'username' => 'required|string|min:3|max:16|regex:/^[\S]+$/|unique:users',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // Fetch the 'basic' role UUID
        $basicRoleId = DB::table('roles')->where('name', 'basic')->value('id');
        $fields['role_id'] = $basicRoleId;

        // Create the user
        $user = User::create($fields);
        $token = $user->createToken($request->username);

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken
        ], 201);
    }


    // Login user

    public function login(Request $request){

        // Validate input fields
        $request->validate([
            'email' => ['required', 'email', 'exists:users'],
            'password' => ['required']
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }

        $token = $user->createToken($user->username);

        return response()->json([
            'user' => $user,
            'token' => $token,
            'redirect_url' => '/api/users/userprofile'
        ], 200); // Successful login response

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
