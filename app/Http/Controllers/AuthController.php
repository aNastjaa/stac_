<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\SponsorSubmission;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Vote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Profiler\Profile;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $fields = $request->validate([
            'username' => 'required|string|min:3|max:16|regex:/^[\S]+$/|unique:users',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
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

    /**
     * Login a user and generate a token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
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

    /**
     * Logout the user and invalidate their token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // Invalidate all user tokens
        $request->user()->tokens()->delete();

        // Log out the user from the session if any
        Auth::logout();

        return response()->json(['message' => 'You are logged out.']);
    }
    /**
     * Delete the logged-in user's account and all related data.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();

        // Delete the user's related data (posts, submissions, comments, likes, etc.)
        Post::where('user_id', $user->id)->delete();
        SponsorSubmission::where('user_id', $user->id)->delete();
        Comment::where('user_id', $user->id)->delete();
        Like::where('user_id', $user->id)->delete();
        Vote::where('user_id', $user->id)->delete();
        UserProfile::where('user_id', $user->id)->delete();

        // Delete user tokens (invalidate sessions)
        $user->tokens()->delete();

        // Finally, delete the user
        $user->delete();

        // Log the user deletion event
        Log::info('User and all related data deleted:', ['user' => $user->toArray()]);

        // Return success message
        return response()->json(['message' => 'Your account and all related data have been deleted successfully.']);
    }
}
