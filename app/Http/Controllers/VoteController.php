<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    /**
     * Store a vote for a specific submission.
     *
     * @param  Request  $request
     * @param  string  $challengeId
     * @param  string  $submissionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, string $challengeId, string $submissionId)
    {
        $userId = Auth::id();

        // Check if the user has already voted for this submission
        if (Vote::where('user_id', $userId)->where('submission_id', $submissionId)->exists()) {
            return response()->json(['error' => 'User has already voted for this submission'], 400);
        }

        // Create the vote
        $vote = Vote::create([
            'user_id' => $userId,
            'submission_id' => $submissionId,
        ]);

        return response()->json($vote, 201);
    }

    /**
     * Get all votes for a specific submission.
     *
     * @param  string  $challengeId
     * @param  string  $submissionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(string $challengeId, string $submissionId)
    {
        // Get all votes for the submission, including user data
        $votes = Vote::where('submission_id', $submissionId)
            ->with('user') // Assuming you have a relationship defined for 'user' in the Vote model
            ->get();

        return response()->json($votes);
    }

    /**
     * Delete a vote for a specific submission.
     *
     * @param  string  $challengeId
     * @param  string  $submissionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $challengeId, string $submissionId)
    {
        $userId = Auth::id();

        // Find the vote the user has placed for the specific submission
        $vote = Vote::where('user_id', $userId)->where('submission_id', $submissionId)->first();

        // If the vote does not exist, return an error
        if (!$vote) {
            return response()->json(['error' => 'Vote not found'], 404);
        }

        // Delete the vote
        $vote->delete();

        return response()->json(['message' => 'Vote deleted successfully'], 200);
    }
}
