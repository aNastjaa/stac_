<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LikeController extends Controller
{
    /**
     * Add a like to the specified artwork.
     *
     * @param  Request  $request
     * @param  string  $id  The UUID of the post being liked.
     * @return JsonResponse
     */
    public function store(Request $request, string $id): JsonResponse
    {
        $post = Post::findOrFail($id);

        // Check if the user has already liked this post
        $existingLike = Like::where('user_id', Auth::id())->where('post_id', $post->id)->first();
        if ($existingLike) {
            return response()->json(['message' => 'You have already liked this post.'], 400);
        }

        $like = new Like([
            'user_id' => Auth::id(),  // Automatically use the authenticated user's ID
            'post_id' => $post->id,
        ]);
        $like->save();

        return response()->json($like, 201);
    }

    /**
     * Get all likes for the specified artwork.
     *
     * @param  string  $id  The UUID of the post whose likes are being retrieved.
     * @return JsonResponse
     */
    public function index(string $id): JsonResponse
    {
        $post = Post::findOrFail($id);
        $likes = $post->likes;

        return response()->json($likes);
    }

    /**
     * Remove a like from the specified artwork.
     *
     * @param string $postId
     * @param  string  $likeId
     * @return JsonResponse
     */
    public function destroy(string $postId, string $likeId): JsonResponse
    {
        Log::info('Authenticated User ID:', ['auth_id' => Auth::id()]);
        Log::info('Attempting to delete like with ID: ' . $likeId);
    
        $like = Like::findOrFail($likeId);
        Log::info('Found like:', ['like' => $like]);
    
        if ($like->user_id !== Auth::id()) {
            Log::warning('Unauthorized like deletion attempt', [
                'like_id' => $likeId,
                'user_id' => Auth::id(),
                'expected_user' => $like->user_id,
            ]);
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    
        $like->delete();
        return response()->json([], 204);
    }    

    /**
     * Check if the authenticated user has liked the specified artwork.
     *
     * @param string $id  The UUID of the post being checked.
     * @return JsonResponse
     */
    public function checkIfUserLikedPost(string $id): JsonResponse
    {
        $post = Post::findOrFail($id);

        // Check if the user has liked this post
        $liked = Like::where('user_id', Auth::id())->where('post_id', $post->id)->exists();

        return response()->json(['liked' => $liked]);
    }

}
