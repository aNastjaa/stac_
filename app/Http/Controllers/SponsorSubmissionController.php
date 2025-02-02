<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSponsorSubmissionRequest;
use App\Http\Requests\UpdateSponsorSubmissionRequest;
use App\Models\SponsorChallenge;
use App\Models\SponsorSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Stringable;

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
            // Fetch submissions for the given challenge
            $submissions = SponsorSubmission::where('challenge_id', $challengeId)
                ->with('user')  // Eager load user data
                ->get();

            return response()->json($submissions);
        } catch (\Exception $e) {
            Log::error("Error fetching submissions: " . $e->getMessage());
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
    public function store(StoreSponsorSubmissionRequest $request): JsonResponse
    {
        try {
            // Ensure the user is authenticated and is a 'pro' user
            $user = Auth::user();
            if (!$user || $user->role->name !== 'pro') {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
    
            // Log user information for debugging
            Log::info('Authenticated Pro User:', ['user' => $user]);
    
            // Ensure the user is submitting to a valid challenge (if needed)
            $challenge = SponsorChallenge::find($request->challengeId);
            if (!$challenge) {
                return response()->json(['message' => 'Challenge not found'], 404);
            }
    
            // Validate and store the image via the validated request
            if (!$request->hasFile('image')) {
                return response()->json(['message' => 'Image is required'], 422); // Error if no image is provided
            }
    
            // Store the image in the public storage
            $image = $request->file('image');
            $imagePath = $image->store('submissions', 'public'); // Store in 'public/submissions' folder
    
            Log::info('Image stored at: ' . $imagePath);  // Log image path for debugging
    
            // Create the submission with the uploaded image path
            $submission = SponsorSubmission::create([
                'user_id' => $user->id,
                'challenge_id' => $request->challengeId, // Associate with challenge ID from the request
                'description' => $request->description,
                'image_path' => $imagePath, // Directly store the relative path
                'status' => 'pending', // Default status as 'pending'
            ]);
    
            return response()->json($submission, 201); // Return the created submission with a 201 status code
        } catch (\Exception $e) {
            // Log the error if there's an issue
            Log::error('Error creating submission: ' . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error'], 500);
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
            $submission = SponsorSubmission::with('user')
                ->where([
                    ['id', '=', $submissionId],
                    ['challenge_id', '=', $challengeId]
                ])->firstOrFail();

            return response()->json([
                'id' => $submission->id,
                'user_id' => $submission->user_id,
                'username' => $submission->user->username,
                'avatar_url' => $submission->user->avatar_url,
                'image_path' => Storage::url($submission->image_path), // Return the public URL of the stored file
                'description' => $submission->description,
                'status' => $submission->status,
                'created_at' => $submission->created_at,
                'updated_at' => $submission->updated_at,
            ]);
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

            $submission->update($request->only(['image_url', 'description'])); // You may need to handle image upload here separately

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

            // Remove the uploaded file from storage if necessary
            if (Storage::exists($submission->image_path)) {
                Storage::delete($submission->image_path);
            }

            $submission->delete();

            return response()->json(['message' => 'Submission deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Submission not found'], 404);
        }
    }
}
