<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreUploadRequest;
use App\Http\Requests\UpdateUploadRequest;
use Dotenv\Util\Regex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    // List all uploads and return as JSON
    public function index()
    {
        $uploads = Upload::all();
        Log::info('Fetched uploads:', ['uploads' => $uploads]);
        return response()->json($uploads);
    }

    // Store a new upload for avatars
    public function uploadAvatar(StoreUploadRequest $request)
    {
        $filePath = $request->file('file')->store('avatar', 'public');
        $upload = Upload::create([
            'file_url' => $filePath,
            'file_type' => 'avatar',
        ]);

        return response()->json($upload, 201);
    }

    // Store a new upload for sponsor brand logos
    public function uploadBrandLogo(StoreUploadRequest $request)
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
    public function update(UpdateUploadRequest $request, $uploadId = null)
    {
        // Fetch the existing upload by ID
        $upload = Upload::findOrFail($uploadId);

        // Handle file upload logic
        if ($request->hasFile('file')) {
            // Validate and store the file
            $path = $request->file('file')->store('uploads', 'public');
            // Update the upload record with the new file path
            $upload->file_url = $path;

            // Update other attributes as necessary
            $upload->file_type = $request->input('file_type');
            $upload->save();

            return response()->json(['message' => 'Upload updated successfully', 'upload' => $upload], 200);
        }

        return response()->json(['message' => 'File is missing'], 400);
    }


    // Delete an upload
    public function destroy(Upload $upload)
    {
        Storage::disk('public')->delete($upload->file_url);
        $upload->delete();
        return response()->json(['message' => 'Upload deleted successfully']);
    }
}
