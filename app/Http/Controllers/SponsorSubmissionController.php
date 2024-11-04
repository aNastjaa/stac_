<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSponsorSubmissionRequest;
use App\Http\Requests\UpdateSponsorSubmissionRequest;
use App\Models\SponsorChallenge;
use App\Models\SponsorSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SponsorSubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($challengeId)
    {
        Log::info('Fetching submissions for sponsor challenge', ['challenge_id' => $challengeId]);

        try {
            $submissions = SponsorSubmission::where('challenge_id', $challengeId)
                ->with('user')
                ->get();

            Log::info('Fetched all submissions', ['submissions' => $submissions]);

            return response()->json($submissions);
        } catch (\Exception $e) {
            Log::error('Error fetching submissions', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch submissions'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSponsorSubmissionRequest $request, $challengeId)
    {
        Log::info('Store method called', [
            'challenge_id' => $challengeId,
            'user_id' => Auth::id(),
            'request_data' => $request->all(),
        ]);

        try {
            $user = Auth::user();
            if (!$user) {
                Log::warning('User not authenticated when trying to submit', ['challenge_id' => $challengeId]);
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $submission = SponsorSubmission::create([
                'user_id' => $user->id,
                'challenge_id' => $challengeId,
                'image_url' => $request->input('image_url'),
                'description' => $request->input('description'),
            ]);

            Log::info('Submission created successfully', ['submission_id' => $submission->id]);

            return response()->json($submission, 201);
        } catch (\Exception $e) {
            Log::error('Error creating submission', [
                'error' => $e->getMessage(),
                'challenge_id' => $challengeId,
                'user_id' => Auth::id(),
            ]);
            return response()->json(['error' => 'Failed to create submission'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($challengeId, $submissionId)
    {
        Log::info('Fetching specific sponsor submission', ['challenge_id' => $challengeId, 'submission_id' => $submissionId]);

        try {
            $submission = SponsorSubmission::with('user', 'sponsorChallenge')->where([
                ['id', '=', $submissionId],
                ['challenge_id', '=', $challengeId]
            ])->firstOrFail();

            Log::info('Fetched specific sponsor submission', ['submission_id' => $submissionId]);

            return response()->json($submission);
        } catch (\Exception $e) {
            Log::error('Error fetching submission', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Submission not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSponsorSubmissionRequest $request, $challengeId, $submissionId)
    {
        try {
            $submission = SponsorSubmission::where([
                ['id', '=', $submissionId],
                ['challenge_id', '=', $challengeId]
            ])->firstOrFail();

            $submission->update($request->only(['image_url', 'description']));

            Log::info('Submission updated successfully', ['submission_id' => $submissionId]);

            return response()->json($submission, 200);
        } catch (\Exception $e) {
            Log::error('Error updating submission', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Submission not found'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($challengeId, $submissionId)
    {
        try {
            $submission = SponsorSubmission::where([
                ['id', '=', $submissionId],
                ['challenge_id', '=', $challengeId]
            ])->firstOrFail();

            $submission->delete();

            Log::info('Submission deleted successfully', ['submission_id' => $submissionId]);

            return response()->json(['message' => 'Submission deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting submission', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Submission not found'], 404);
        }
    }
}
