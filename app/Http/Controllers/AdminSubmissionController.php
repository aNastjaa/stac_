<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\SponsorSubmission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AdminSubmissionController extends Controller
{
    /**
     * Update the status of a post (approve/reject).
     *
     * @param Request $request
     * @param string $postId
     * @return JsonResponse
     */
    public function updatePostStatus(Request $request, string $postId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:accepted,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $post = Post::findOrFail($postId);
        $post->status = $request->status;
        $post->save();

        $message = $request->status === 'accepted'
            ? 'Post accepted successfully.'
            : 'Post rejected successfully.';

        return response()->json(['message' => $message]);
    }

    /**
     * Update the status of a sponsor submission (approve/reject).
     *
     * @param Request $request
     * @param string $submissionId
     * @return JsonResponse
     */
    public function updateSubmissionStatus(Request $request, string $submissionId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:accepted,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $submission = SponsorSubmission::findOrFail($submissionId);
        $submission->status = $request->status;
        $submission->save();

        $message = $request->status === 'accepted'
            ? 'Submission accepted successfully.'
            : 'Submission rejected successfully.';

        return response()->json(['message' => $message]);
    }

    /**
     * View pending posts.
     *
     * @return JsonResponse
     */
    public function viewPendingPosts(): JsonResponse
    {
        $pendingPosts = Post::where('status', 'pending')->get();
        return response()->json($pendingPosts);
    }

    /**
     * View pending submissions.
     *
     * @return JsonResponse
     */
    public function viewPendingSubmissions(): JsonResponse
    {
        $pendingSubmissions = SponsorSubmission::where('status', 'pending')->get();
        return response()->json($pendingSubmissions);
    }
}
