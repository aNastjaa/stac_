<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserProfileController extends Controller
{
    /**
     * Display a listing of all user profiles.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $profiles = UserProfile::all();

        return response()->json([
            'profiles' => $profiles,
        ]);
    }

    /**
     * Display a specific user's profile.
     *
     * @param  string  $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $profile = UserProfile::find($id);

        if (!$profile) {
            return response()->json(['error' => 'Profile not found.'], 404);
        }

        return response()->json(['profile' => $profile], 200);
    }

    /**
     * Store a newly created profile for the authenticated user, deleting any previous profile.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'external_links' => 'nullable|array',
        ]);

        // Delete any existing profile for the user
        UserProfile::where('user_id', $user->id)->delete();

        try {
            $profile = UserProfile::create([
                'user_id' => $user->id,
                'full_name' => $validatedData['full_name'],
                'bio' => $validatedData['bio'],
                'external_links' => $validatedData['external_links'],
            ]);

            return response()->json(['profile' => $profile], 201);
        } catch (\Exception $e) {
            Log::error('Error creating user profile', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error creating profile'], 500);
        }
    }

    /**
     * Update the authenticated user's profile.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'external_links' => 'nullable|array',
        ]);

        $userProfile = UserProfile::where('user_id', $request->user()->id)->first();

        if (!$userProfile) {
            return response()->json(['error' => 'Profile not found.'], 404);
        }

        $userProfile->update($validatedData);

        return response()->json(['message' => 'Profile updated successfully.', 'profile' => $userProfile], 200);
    }

    /**
     * Delete a specific user profile.
     *
     * @param  string  $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $profile = UserProfile::find($id);

        if (!$profile) {
            return response()->json(['error' => 'Profile not found.'], 404);
        }

        $profile->delete();

        return response()->json(['message' => 'Profile deleted successfully.'], 200);
    }
}
