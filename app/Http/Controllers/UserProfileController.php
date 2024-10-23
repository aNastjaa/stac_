<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class UserProfileController extends Controller {

    public function index(Request $request)
    {
        // Ensure the request is authenticated via Sanctum
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Retrieve the user's profile
        $profile = $user->profile;

        return response()->json([
            'profile' => $profile,
        ]);
    }

    public function store(Request $request)
{
    // Ensure the request is authenticated via Sanctum
    $user = Auth::user(); // This should return the User model instance

    // Check if user is authenticated
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    // Log user information for debugging
    Log::info('Authenticated User:', ['user' => $user]);

    $request->validate([
        'full_name' => 'required|string|max:255',
        'bio' => 'nullable|string',
        'external_links' => 'nullable|array',
    ]);

    // Create the user profile
    try {
        $profile = UserProfile::create([
            'user_id' => $user->id, // This should now be the correct UUID
            'full_name' => $request->full_name,
            'bio' => $request->bio,
            'external_links' => $request->external_links,
        ]);

        return response()->json($profile, 201);
    } catch (\Exception $e) {
        // Log any errors for further debugging
        Log::error('Error creating user profile:', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Error creating profile'], 500);
    }
}

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'external_links' => 'nullable|array',
        ]);

        // Find the user's profile
        $userProfile = UserProfile::where('user_id', $request->user()->id)->first();

        if (!$userProfile) {
            return response()->json(['error' => 'Profile not found.'], 404);
        }

        // Update the profile with validated data
        $userProfile->update($validatedData);

        return response()->json(['message' => 'Profile updated successfully.', 'profile' => $userProfile], 200);
    }


}
