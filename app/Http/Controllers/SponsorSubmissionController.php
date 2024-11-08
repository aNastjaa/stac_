<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSponsorSubmissionRequest;
use App\Http\Requests\UpdateSponsorSubmissionRequest;
use App\Models\SponsorSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SponsorSubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  string  $challengeId
     * @return JsonResponse
     */
    public function index(string $challengeId): JsonResponse
    {
        try {
            $submissions = SponsorSubmission::where('challenge_id', $challengeId)
                ->with('user')
                ->get();

            return response()->json($submissions);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch submissions'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreSponsorSubmissionRequest  $request
     * @param  string  $challengeId
     * @return JsonResponse
     */
    public function store(StoreSponsorSubmissionRequest $request, string $challengeId): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $submission = SponsorSubmission::create([
                'user_id' => $user->id,
                'challenge_id' => $challengeId,
                'image_url' => $request->input('image_url'),
                'description' => $request->input('description'),
            ]);

            return response()->json($submission, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create submission'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $challengeId
     * @param  string  $submissionId
     * @return JsonResponse
     */
    public function show(string $challengeId, string $submissionId): JsonResponse
    {
        try {
            $submission = SponsorSubmission::with('user', 'sponsorChallenge')
                ->where([
                    ['id', '=', $submissionId],
                    ['challenge_id', '=', $challengeId]
                ])->firstOrFail();

            return response()->json($submission);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Submission not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateSponsorSubmissionRequest  $request
     * @param  string  $challengeId
     * @param  string  $submissionId
     * @return JsonResponse
     */
    public function update(UpdateSponsorSubmissionRequest $request, string $challengeId, string $submissionId): JsonResponse
    {
        try {
            $submission = SponsorSubmission::where([
                ['id', '=', $submissionId],
                ['challenge_id', '=', $challengeId]
            ])->firstOrFail();

            $submission->update($request->only(['image_url', 'description']));

            return response()->json($submission, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Submission not found'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $challengeId
     * @param  string  $submissionId
     * @return JsonResponse
     */
    public function destroy(string $challengeId, string $submissionId): JsonResponse
    {
        try {
            $submission = SponsorSubmission::where([
                ['id', '=', $submissionId],
                ['challenge_id', '=', $challengeId]
            ])->firstOrFail();

            $submission->delete();

            return response()->json(['message' => 'Submission deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Submission not found'], 404);
        }
    }
}
