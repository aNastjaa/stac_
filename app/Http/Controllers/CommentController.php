<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a newly created comment in storage.
     *
     * @param  string  $postId
     * @param  StoreCommentRequest  $request
     * @return JsonResponse
     */
    public function store(string $postId, StoreCommentRequest $request): JsonResponse
    {
        // Ensure the user is authenticated
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Create the comment
        $comment = Comment::create([
            'user_id' => $user->id,
            'post_id' => $postId,
            'comment_text' => $request->comment_text,
        ]);

        return response()->json($comment, 201);
    }

    /**
     * Display a listing of comments for a specific artwork.
     *
     * @param  string  $postId
     * @return JsonResponse
     */
    public function index(string $postId): JsonResponse
    {
        $comments = Comment::with('user')->where('post_id', $postId)->get();
        return response()->json($comments);
    }

    /**
     * Update the specified comment in storage.
     *
     * @param  Request  $request
     * @param  string  $commentId
     * @return JsonResponse
     */
    public function update(Request $request, string $postId, string $commentId): JsonResponse
    {
        // Fetch the comment by both postId and commentId
        $comment = Comment::where('post_id', $postId)->where('id', $commentId)->firstOrFail();

        // Ensure the user is the owner of the comment
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validate request data
        $validatedData = $request->validate([
            'comment_text' => 'required|string|max:255',
        ]);

        $comment->update($validatedData);

        return response()->json($comment);
    }

    /**
     * Remove the specified comment from storage.
     *
     * @param  string  $postId
     * @param  string  $commentId
     * @return JsonResponse
     */
    public function destroy(string $postId, string $commentId): JsonResponse
    {
        $comment = Comment::where('post_id', $postId)->where('id', $commentId)->firstOrFail();

        // Ensure the user is the owner of the comment
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
