<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Models\Theme;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    /**
     * Store a newly created post in storage.
     *
     * @param  StorePostRequest  $request
     * @return JsonResponse
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        // Ensure the user is authenticated
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Log user information for debugging
        Log::info('Authenticated User:', ['user' => $user]);

        // Fetch the current theme (if exists)
        $currentTheme = Theme::where('start_date', '<=', now())
            ->orderBy('start_date', 'desc')
            ->firstOrFail();

        // Create the post
        $post = Post::create([
            'user_id' => $user->id,
            'theme_id' => $currentTheme->id, // Use the dynamic theme ID
            'image_url' => $request->image_url,
            'description' => $request->description,
        ]);

        return response()->json($post, 201);
    }

    /**
     * Display a listing of the posts.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $posts = Post::with(['user', 'theme'])->get(); // Load relationships efficiently
        return response()->json($posts);
    }

    /**
     * Display the specified post.
     *
     * @param  string  $postId  The ID of the post
     * @return JsonResponse
     */
    public function show(string $postId): JsonResponse
    {
        $post = Post::with(['user', 'theme'])->findOrFail($postId);
        return response()->json($post);
    }

    /**
     * Update the specified post in storage.
     *
     * @param  UpdatePostRequest  $request
     * @param  string  $postId  The ID of the post
     * @return JsonResponse
     */
    public function update(UpdatePostRequest $request, string $postId): JsonResponse
    {
        $post = Post::findOrFail($postId);

        // Ensure the user is the owner of the post
        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validate request data
        $validatedData = $request->validated();
        $post->update($validatedData);

        return response()->json($post);
    }

    /**
     * Remove the specified post from storage.
     *
     * @param  string  $postId  The ID of the post
     * @return JsonResponse
     */
    public function destroy(string $postId): JsonResponse
    {
        $post = Post::findOrFail($postId);

        // Ensure the user is the owner of the post
        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
