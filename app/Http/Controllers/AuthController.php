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
            'username' => [
                'required',
                'string',
                'min:3', // At least 4 characters
                'max:16', // Maximum 16 characters
                'regex:/^[\S]+$/', // No spaces allowed
                'unique:users', // Must not yet exist in the database
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns', // Must be a valid email format, DNS check included
                'max:255',
                'unique:users', // Must not yet exist in the database
            ],
            'password' => [
                'required',
                'string',
                'min:8', // At least 8 characters
                'regex:/[a-z]/', // Must contain at least one lowercase letter
                'regex:/[A-Z]/', // Must contain at least one uppercase letter
                'regex:/[0-9]/', // Must contain at least one number
                'regex:/[\W]/', // Must contain at least one special character
                'regex:/^\S*$/', // No spaces allowed
                'confirmed', // Must match the password confirmation field
            ]
        ]);

        // Fetch the 'basic' role UUID
        $basicRoleId = DB::table('roles')->where('name', 'basic')->value('id');

        // Add role_id to fields array
        $fields['role_id'] = $basicRoleId;

        // Create the user with mass assignment
        $user = User::create($fields);

        //Log in
        Auth::login($user);

        // Redirect or return a response
        return redirect()->route('login')->with('success', 'Registration successful! Please log in.');

    }

    // Login user

    public function login(Request $request){

        // Validate input fields
        $fields = $request->validate([
            'email' => ['required', 'max:255', 'email'],
            'password' => ['required', 'min:3'],
        ]);

        if(Auth::attempt($fields, $request->remember)) {
            return redirect()->intended('profile');
        } else {
           return back()->withErrors([
            'failed' => 'The provided credentials do not match our records'
           ]);
        }
    }

    // Logout
    public function logout(Request $request){
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

}
