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
            $token = $user->createToken('auth_token')->plainTextToken;
    
            // Include the user's role with the response
            $userWithRole = [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role_id' => $user->role_id,  // Assuming `role_id` is stored in the user table
                'role_name' => $user->role->name, // Assuming a relationship exists between User and Role
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];
    
            // If remember me is checked, set a long-lived cookie
            if ($fields['remember_me'] ?? false) {
                $cookie = cookie('sanctum_token', $token, 60 * 24 * 7);  // 7 days long cookie
                return response()->json([
                    'message' => 'Logged in successfully',
                    'user' => $userWithRole,
                    'token' => $token, // Ensure token is returned in the response
                ])->withCookie($cookie);
            }
    
            return response()->json([
                'message' => 'Logged in successfully',
                'token' => $token, // Send the token in the response
                'user' => $userWithRole, // Include the user role data in the response
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
 * Logout the user by deleting their tokens.
 *
 * @param Request $request
 * @return JsonResponse
 */
    public function logout(Request $request): JsonResponse
    {
        try {
            if ($request->user()) {
                // Revoke all tokens for the user
                $request->user()->tokens()->delete();
                Log::info('User logged out successfully.', ['user_id' => $request->user()->id]);
            }

            return response()->json(['message' => 'You are logged out.'], 200);
        } catch (\Exception $e) {
            Log::error('Error during logout:', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'An error occurred while logging out.'], 500);
        }
    }

    /**
     * Update the authenticated user's email or password.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $user = User::find(Auth::id());

        if (!$user) {
            return response()->json(['message' => 'User not authenticated.'], 401);
        }

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

        $responseMessage = [];

        if ($request->filled('email')) {
            $user->email = $validatedData['email'];
            $responseMessage['email'] = 'Email has been updated successfully.';
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
            $responseMessage['password'] = 'Password has been updated successfully.';
        }

        if (empty($responseMessage)) {
            return response()->json(['message' => 'No updates made.'], 200);
        }

        $user->save();

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

        Post::where('user_id', $user->id)->delete();
        SponsorSubmission::where('user_id', $user->id)->delete();
        Comment::where('user_id', $user->id)->delete();
        Like::where('user_id', $user->id)->delete();
        Vote::where('user_id', $user->id)->delete();
        UserProfile::where('user_id', $user->id)->delete();

        $user->tokens()->delete();

        $user->delete();

        Log::info('User and all related data deleted:', ['user' => $user->toArray()]);

        return response()->json(['message' => 'Your account and all related data have been deleted successfully.']);
    }
}