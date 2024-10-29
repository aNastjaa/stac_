<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSponsorChallengeRequest;
use App\Http\Requests\UpdateSponsorChallengeRequest;
use App\Models\SponsorChallenge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SponsorChallengeController extends Controller
{
    /**
     * Display a listing of the sponsor challenges.
     */
    public function index(): JsonResponse
    {
        $challenges = SponsorChallenge::all();
        return response()->json($challenges);
    }

    /**
     * Store a newly created sponsor challenge (Admin only).
     */
    public function store(StoreSponsorChallengeRequest $request): JsonResponse
    {
        Log::info('Store Sponsor Challenge request received', $request->all());
        // Check if the user is an admin
        if (Auth::user()->role->name !== 'admin') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Create a new sponsor challenge
        $challenge = SponsorChallenge::create($request->validated());

        return response()->json($challenge, 201);
    }

    /**
     * Display the specified sponsor challenge.
     */
    public function show(SponsorChallenge $sponsorChallenge): JsonResponse
    {
        return response()->json($sponsorChallenge);
    }

    /**
     * Update the specified sponsor challenge (Admin only).
     */
    public function update(UpdateSponsorChallengeRequest $request, SponsorChallenge $sponsorChallenge): JsonResponse
    {
        // Check if the user is an admin
        if (Auth::user()->role->name !== 'admin') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Update the sponsor challenge with validated data
        $sponsorChallenge->update($request->validated());

        return response()->json($sponsorChallenge);
    }

    /**
     * Remove the specified sponsor challenge (Admin only).
     */
    public function destroy(SponsorChallenge $sponsorChallenge): JsonResponse
    {
        // Check if the user is an admin
        if (Auth::user()->role->name !== 'admin') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $sponsorChallenge->delete();

        return response()->json(['message' => 'Challenge deleted successfully.']);
    }
}

