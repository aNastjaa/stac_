<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSponsorChallengeRequest;
use App\Http\Requests\UpdateSponsorChallengeRequest;
use App\Models\SponsorChallenge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;


class SponsorChallengeController extends Controller
{

    public function __construct()
{
    Log::info('SponsorChallengeController instantiated');
}

    /**
     * Display a listing of the sponsor challenges.
     */
    public function index(): JsonResponse
    {
        Log::info('SponsorChallengeController@index called');
        $challenges = SponsorChallenge::all();
        return response()->json($challenges);
    }

    /**
     * Store a newly created sponsor challenge (Admin only).
     */
    public function store(StoreSponsorChallengeRequest $request): JsonResponse
    {
        Log::info('Inside store method of SponsorChallengeController');
        Log::info('Store Sponsor Challenge request received', $request->validated());

        // Check if the user is an admin
        if (Auth::user()->role->name !== 'admin') {
            Log::warning('Unauthorized access attempt to store sponsor challenge', ['user_id' => Auth::id()]);
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Create the new sponsor challenge
        $challenge = SponsorChallenge::create($request->validated());
        Log::info('Sponsor Challenge created successfully', ['challenge_id' => $challenge->id]);

        return response()->json($challenge, 201);
    }

    /**
     * Display the specified sponsor challenge.
     */
    public function show($id): JsonResponse
    {
        $sponsorChallenge = SponsorChallenge::find($id);

        if (!$sponsorChallenge) {
            Log::warning('Sponsor challenge not found', ['challenge_id' => $id]);
            return response()->json(['message' => 'Sponsor challenge not found.'], 404);
        }

        return response()->json($sponsorChallenge);
    }


    /**
     * Update the specified sponsor challenge (Admin only).
     */
    public function update(UpdateSponsorChallengeRequest $request, $id)
    {

        // Attempt to retrieve the SponsorChallenge by ID
        $sponsorChallenge = SponsorChallenge::find($id);

        // Check if the SponsorChallenge was found
        if (!$sponsorChallenge) {
            Log::warning('Sponsor Challenge not found', ['id' => $id]);
            return response()->json(['message' => 'Sponsor Challenge not found'], 404);
        }

        // Validate and update the SponsorChallenge
        $validatedData = $request->validated();
        $sponsorChallenge->update($validatedData);

        return response()->json($sponsorChallenge, 200);
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

