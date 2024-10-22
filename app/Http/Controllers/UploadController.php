<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreUploadRequest;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    // List all uploads and return to view
    public function index()
{
    $uploads = Upload::all(); // Fetch all uploads from the database

    // Log the uploads for debugging (optional)
    Log::info('Fetched uploads:', ['uploads' => $uploads]);

    // Return the uploads as JSON
    return response()->json($uploads);
}


    // Store a new upload for avatars
    public function uploadAvatar(StoreUploadRequest $request) // Use StoreUploadRequest
    {
        $filePath = $request->file('file')->store('avatar', 'public');

        $upload = Upload::create([
            'file_url' => $filePath,
            'file_type' => 'avatar',
        ]);

        return response()->json($upload, 201);
    }

    // Store a new upload for sponsor brand logos
    public function uploadBrandLogo(StoreUploadRequest $request) // Use StoreUploadRequest
    {
        $filePath = $request->file('file')->store('brand-logos', 'public');

        $upload = Upload::create([
            'file_url' => $filePath,
            'file_type' => 'brand_logo',
        ]);

        return response()->json($upload, 201);
    }

    // Show a specific upload
    public function show(Upload $upload)
    {
        return response()->json($upload);
    }

    // Update an existing upload
    public function update(Request $request, $id)
    {
        $upload = Upload::find($id);

        if (!$upload) {
            return response()->json(['message' => 'Upload not found'], 404);
        }

        if ($request->hasFile('file')) {
            // Delete the old file
            Storage::disk('public')->delete($upload->file_url);

            // Store the new file
            $file = $request->file('file');
            $path = $file->store($upload->file_type === 'avatar' ? 'avatar' : 'brand-logos', 'public');

            // Update the record
            $upload->file_url = $path;
            $upload->file_type = $upload->file_type; // Adjust this if necessary

            $upload->save();

            return response()->json(['message' => 'Upload updated successfully', 'upload' => $upload], 200);
        }

        return response()->json(['message' => 'No file uploaded or no changes made'], 400);
    }

    // Delete an upload
    public function destroy(Upload $upload)
    {
        Storage::disk('public')->delete($upload->file_url);
        $upload->delete();

        return response()->json(['message' => 'Upload deleted successfully']);
    }
}
