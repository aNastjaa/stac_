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
use Illuminate\Validation\Rules\Password;

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
   
    try {
        $fields = $request->validate([
            'username' => 'required|string|min:3|max:16|regex:/^[\S]+$/|unique:users',
            'email' => 'required|string|max:255|unique:users',
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ]);

        $basicRoleId = DB::table('roles')->where('name', 'basic')->value('id');
        $fields['role_id'] = $basicRoleId;

        // Create the user
        $user = User::create($fields);

        Log::info('New User Created:', ['user' => $user->toArray()]);

        return response()->json([
            'user' => $user,
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation Error:', ['errors' => $e->errors()]);
        return response()->json([
            'message' => 'Validation Error',
            'errors' => $e->errors(),
        ], 422); // Unprocessable Entity status
    } catch (\Exception $e) {
        Log::error('Unexpected Error:', ['error' => $e->getMessage()]);
        return response()->json([
            'message' => 'An unexpected error occurred. Please try again later.',
        ], 500); // Internal Server Error
    }
}


/**
 * Login a user with either token or session.
 *
 * @param Request $request
 * @return JsonResponse
 */
public function login(Request $request): JsonResponse
    {

        Log::info('Login function accessed', ['request_headers' => $request->headers->all()]);
        
        try {
            $fields = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
                'remember_me' => 'boolean',
            ]);

            // Attempt to authenticate the user
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json(['message' => 'Invalid login details'], 401);
            }

            $user = $request->user();

            // Handle 'remember me' functionality if provided
            if ($fields['remember_me'] ?? false) {
               $token = $user->createToken('auth_token')->plainTextToken;
                $cookie = cookie('sanctum_token');

                return response()->json([
                    'message' => 'Logged in successfully',
                    'user' => $user,
                    'token' => $token
                ])->withCookie($cookie);
            }

            return response()->json([
                'message' => 'Logged in successfully',
                'token' => $user->createToken('auth_token')->plainTextToken,
                'user' => $user,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred. Please try again later.',
            ], 500);
        }
    }
    /**
     * Logout the user from session and invalidate their token if any.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        if ($request->user()) {
            // Invalidate all user tokens
            $request->user()->tokens()->delete();
        }

        // Logout from session-based auth
        Auth::logout();
        Log::info('User logged out.');

        return response()->json(['message' => 'You are logged out.'], 200);
    }

    /**
     * Update the authenticated user's email or password.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        // Retrieve the authenticated user by ID
        $user = User::find(Auth::id());

        // Check if the user is authenticated
        if (!$user) {
            return response()->json(['message' => 'User not authenticated.'], 401);
        }

        // Validate email and password fields separately
        $validatedData = $request->validate([
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'password' => [
                'nullable',
                'string',
                Password::min(8)
                ->mixedCase()
                ->numbers()->symbols()
                ->uncompromised(),
            ],
        ]);

        // Initialize response messages
        $responseMessage = [];

        // Update email if provided
        if ($request->filled('email')) {
            $user->email = $validatedData['email'];
            $responseMessage['email'] = 'Email has been updated successfully.';
        }

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
            $responseMessage['password'] = 'Password has been updated successfully.';
        }

        // If neither email nor password was updated
        if (empty($responseMessage)) {
            return response()->json(['message' => 'No updates made.'], 200);
        }

        // Save the updated user data
        $user->save();

        // Return response with appropriate messages for email and password
        return response()->json($responseMessage, 200);
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
