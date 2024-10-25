<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Add a like to the specified artwork.
     */
    public function store(Request $request, $id): JsonResponse
    {
        $post = Post::findOrFail($id);
        $like = new Like([
            'user_id' => Auth::id(),  // Automatically use the authenticated user's ID
            'post_id' => $post->id,
        ]);
        $like->save();

        return response()->json($like, 201);
    }

    /**
     * Get all likes for the specified artwork.
     */
    public function index($id): JsonResponse
    {
        $post = Post::findOrFail($id);
        $likes = $post->likes; // Assuming you have defined a relationship in the Post model

        return response()->json($likes);
    }

    /**
     * Remove a like from the specified artwork.
     */
    public function destroy($id): JsonResponse
    {
        $like = Like::findOrFail($id);

        // Ensure the user is the owner of the like
        if ($like->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $like->delete();
        return response()->json(null, 204);
    }
}

