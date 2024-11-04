<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    /**
     * Store a vote for a specific submission.
     */
    public function store(Request $request, $challengeId, $submissionId)
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
     */
    public function index($challengeId, $submissionId)
    {
        $votes = Vote::where('submission_id', $submissionId)->with('user')->get();
        return response()->json($votes);
    }

    /**
     * Delete a vote for a specific submission.
     */
    public function destroy($challengeId, $submissionId)
    {
        $userId = Auth::id();

        $vote = Vote::where('user_id', $userId)->where('submission_id', $submissionId)->first();

        if (!$vote) {
            return response()->json(['error' => 'Vote not found'], 404);
        }

        $vote->delete();
        return response()->json(['message' => 'Vote deleted successfully'], 200);
    }
}
