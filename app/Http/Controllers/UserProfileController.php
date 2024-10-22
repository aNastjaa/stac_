<?php
namespace App\Http\Controllers;
use App\Models\User;

use Illuminate\Http\Request;

class UserProfileController extends Controller {

    public function index(Request $request)
    {
        // Ensure the request is authenticated via Sanctum
        $user = $request->user(); // This retrieves the authenticated user with Sanctum

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Assuming user profile is in a related 'profile' model, or adjust as per your database
        $profile = $user->profile ?? [
            'name' => $user->username, // Modify fields as necessary
            'email' => $user->email,
            // Add any additional fields you want to return
        ];

        return response()->json([
            'profile' => $profile,
        ]);
    }
}
