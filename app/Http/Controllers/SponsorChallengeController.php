<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSponsorChallengeRequest;
use App\Http\Requests\UpdateSponsorChallengeRequest;
use App\Models\SponsorChallenge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class SponsorChallengeController extends Controller
{
    /**
     * Display a listing of the sponsor challenges.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $challenges = SponsorChallenge::all();
        return response()->json($challenges);
    }

    /**
     * Store a newly created sponsor challenge (Admin only).
     *
     * @param  StoreSponsorChallengeRequest  $request
     * @return JsonResponse
     */
    public function store(StoreSponsorChallengeRequest $request): JsonResponse
    {
        // Check if the user is an admin
        if (Auth::user()->role->name !== 'admin') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Create the new sponsor challenge
        $challenge = SponsorChallenge::create($request->validated());
        return response()->json($challenge, 201);
    }

    /**
     * Display the specified sponsor challenge.
     *
     * @param  int  $challengeId
     * @return JsonResponse
     */
    public function show($challengeId): JsonResponse
    {
        $sponsorChallenge = SponsorChallenge::find($challengeId);

        if (!$sponsorChallenge) {
            return response()->json(['message' => 'Sponsor challenge not found.'], 404);
        }

        return response()->json($sponsorChallenge);
    }

    /**
     * Update the specified sponsor challenge (Admin only).
     *
     * @param  UpdateSponsorChallengeRequest  $request
     * @param  int  $challengeId
     * @return JsonResponse
     */
    public function update(UpdateSponsorChallengeRequest $request, $challengeId): JsonResponse
    {
        // Attempt to retrieve the SponsorChallenge by ID
        $sponsorChallenge = SponsorChallenge::find($challengeId);

        // Check if the SponsorChallenge was found
        if (!$sponsorChallenge) {
            return response()->json(['message' => 'Sponsor Challenge not found'], 404);
        }

        // Validate and update the SponsorChallenge
        $validatedData = $request->validated();
        $sponsorChallenge->update($validatedData);

        return response()->json($sponsorChallenge, 200);
    }

    /**
     * Remove the specified sponsor challenge (Admin only).
     *
     * @param  int  $challengeId
     * @return JsonResponse
     */
    public function destroy($challengeId): JsonResponse
    {
        // Check if the user is an admin
        if (Auth::user()->role->name !== 'admin') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Attempt to retrieve the SponsorChallenge by ID
        $sponsorChallenge = SponsorChallenge::find($challengeId);

        // Check if the SponsorChallenge was found
        if (!$sponsorChallenge) {
            return response()->json(['message' => 'Sponsor Challenge not found'], 404);
        }

        $sponsorChallenge->delete();

        return response()->json(['message' => 'Challenge deleted successfully.'], 200);
    }
}
