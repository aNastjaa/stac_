<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Models\Theme;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    /**
     * Store a newly created post in storage.
     *
     * @param  StorePostRequest  $request
     * @return JsonResponse
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        try {
            // Ensure the user is authenticated
            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
    
            // Log user information for debugging
            Log::info('Authenticated User:', ['user' => $user]);
    
            // Fetch the current theme (if exists)
            $currentTheme = Theme::where('start_date', '<=', now())
                ->orderBy('start_date', 'desc')
                ->firstOrFail();
    
            // Validate and store the image via the validated request
            if (!$request->hasFile('image')) {
                return response()->json(['message' => 'Image is required'], 422); // Error if no image is provided
            }
    
            // Store the image in the public storage
            $image = $request->file('image');
            $imagePath = $image->store('artworks', 'public'); // Store in 'public/artworks' folder
    
            Log::info('Image stored at: ' . $imagePath);  // Log image path for debugging
    
            // Create the post with the uploaded image path
            $post = Post::create([
                'user_id' => $user->id,
                'theme_id' => $currentTheme->id, // Use the dynamic theme ID
                'image_path' => Storage::url($imagePath), // Store the file URL/path
                'description' => $request->description,
            ]);
    
            return response()->json($post, 201);
        } catch (\Exception $e) {
            // Log the error if there's an issue
            Log::error('Error creating post: ' . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
     
    /**
     * Display a listing of the posts.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $posts = Post::with(['user', 'theme'])->get(); // Get all posts regardless of status
            return response()->json($posts);
        } catch (\Exception $e) {
            Log::error('Error fetching posts: ', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to fetch posts.'], 500);
        }
    }
    /**
     * Display the specified post.
     *
     * @param  string  $postId  The ID of the post
     * @return JsonResponse
     */
    public function show(string $postId): JsonResponse
    {
        try {
            $post = Post::with(['user', 'theme'])->findOrFail($postId);
            return response()->json($post);
        } catch (\Exception $e) {
            Log::error('Error fetching post: ', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Post not found.'], 404);
        }
    }

    /**
     * Update the specified post in storage.
     *
     * @param  UpdatePostRequest  $request
     * @param  string  $postId  The ID of the post
     * @return JsonResponse
     */
    public function update(UpdatePostRequest $request, string $postId): JsonResponse
    {
        try {
            $post = Post::findOrFail($postId);

            // Ensure the user is the owner of the post
            if ($post->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            // Validate request data
            $validatedData = $request->validated();

            // Handle file upload if image is provided
            if ($request->hasFile('image')) {
                // Identify the folder based on the file type
                $currentFolder = 'artworks'; // You can customize the folder as needed

                // Get the current file path stored in the database
                $oldFilePath = $post->image_path;

                // Delete the old file if it exists in the public storage
                if (Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }

                // Store the new file in the appropriate folder
                $imagePath = $request->file('image')->store($currentFolder, 'public');

                // Update the file URL in the database
                $validatedData['image_path'] = Storage::url($imagePath); // Save the file URL/path
            }

            // Update the post with validated data
            $post->update($validatedData);

            return response()->json(['message' => 'Post updated successfully', 'post' => $post], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422); // Validation errors
        } catch (\Exception $e) {
            Log::error('Error updating post: ', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to update post.'], 500);
        }
    }

    /**
     * Remove the specified post from storage.
     *
     * @param  string  $postId  The ID of the post
     * @return JsonResponse
     */
    public function destroy(string $postId): JsonResponse
    {
        try {
            $post = Post::findOrFail($postId);

            // Ensure the user is the owner of the post
            if ($post->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            // Delete the image file if it exists
            if (Storage::disk('public')->exists($post->image_path)) {
                Storage::disk('public')->delete($post->image_path);
            }

            $post->delete();

            return response()->json(['message' => 'Post deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting post: ', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to delete post.'], 500);
        }
    }
}
