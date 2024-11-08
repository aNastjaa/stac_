<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreUploadRequest;
use App\Http\Requests\UpdateUploadRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    /**
     * List all uploads and return as JSON.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $uploads = Upload::all();
        Log::info('Fetched uploads:', ['uploads' => $uploads]);
        return response()->json($uploads);
    }

    /**
     * Store a new upload for avatars.
     *
     * @param StoreUploadRequest $request
     * @return JsonResponse
     */
    public function uploadAvatar(StoreUploadRequest $request): JsonResponse
    {
        $filePath = $request->file('file')->store('avatar', 'public');
        $upload = Upload::create([
            'file_url' => $filePath,
            'file_type' => 'avatar',
        ]);

        return response()->json($upload, 201);
    }

    /**
     * Store a new upload for sponsor brand logos.
     *
     * @param StoreUploadRequest $request
     * @return JsonResponse
     */
    public function uploadBrandLogo(StoreUploadRequest $request): JsonResponse
    {
        $filePath = $request->file('file')->store('brand-logos', 'public');
        $upload = Upload::create([
            'file_url' => $filePath,
            'file_type' => 'brand_logo',
        ]);

        return response()->json($upload, 201);
    }

    /**
     * Show a specific upload.
     *
     * @param Upload $upload
     * @return JsonResponse
     */
    public function show(Upload $upload): JsonResponse
    {
        return response()->json($upload);
    }

    /**
     * Update an existing upload with a new file.
     *
     * This method handles updating an existing upload by deleting the old file
     * and saving the new file in the appropriate folder. It also updates the
     * file's URL and type in the database.
     *
     * @param  \App\Http\Requests\UpdateUploadRequest  $request
     * @param  string  $upload  The UUID of the upload to update
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUploadRequest $request, string $upload): JsonResponse
    {
        // Retrieve the existing upload record by ID
        $upload = Upload::findOrFail($upload);

        // Check if a file has been uploaded in the request
        if ($request->hasFile('file')) {
            // Identify the folder based on the file type (either 'avatar' or 'brand-logos')
            $currentFolder = $upload->file_type === 'avatar' ? 'avatar' : 'brand-logos';

            // Get the current file path stored in the database
            $oldFilePath = $upload->file_url;

            // Delete the old file if it exists in the public storage
            if (Storage::disk('public')->exists($oldFilePath)) {
                Storage::disk('public')->delete($oldFilePath);
            }

            // Store the new file in the correct folder
            $path = $request->file('file')->store($currentFolder, 'public');

            // Update the file URL with the new file's path
            $upload->file_url = $path;

            // Optionally update the file type if provided in the request
            $upload->file_type = $request->input('file_type') ?? $upload->file_type;

            // Save the updated upload record to the database
            $upload->save();

            // Return a successful response with the updated upload details
            return response()->json(['message' => 'Upload updated successfully', 'upload' => $upload], 200);
        }

        // Return an error response if no file is provided
        return response()->json(['message' => 'File is missing'], 400);
    }

    /**
     * Delete an upload.
     *
     * @param Upload $upload
     * @return JsonResponse
     */
    public function destroy(Upload $upload): JsonResponse
    {
        Storage::disk('public')->delete($upload->file_url);
        $upload->delete();
        return response()->json(['message' => 'Upload deleted successfully']);
    }
}
