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
            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
    
            // Fetch the current theme (if exists)
            $currentTheme = Theme::where('start_date', '<=', now())
                ->orderBy('start_date', 'desc')
                ->firstOrFail();
    
            // ✅ Laravel handles validation via StorePostRequest, so no need for manual checks!
    
            // Store the image in public storage
            $image = $request->file('image');
            $imagePath = $image->store('artworks', 'public');
    
            Log::info('Image stored at: ' . $imagePath);
    
            // Create the post with the uploaded image path
            $post = Post::create([
                'user_id' => $user->id,
                'theme_id' => $currentTheme->id,
                'image_path' => Storage::url($imagePath),
                'description' => $request->description,
            ]);
    
            return response()->json([
                'message' => 'Artwork has been posted successfully!',
                'post' => $post
            ], 201);
        } catch (\Exception $e) {
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
            // Fetch posts with user and theme relationships
            $posts = Post::with(['user:id,username', 'theme:id,theme_name'])
                ->withCount(['comments', 'likes']) 
                ->whereIn('status', ['accepted', 'pending'])
                ->get();

            // Return the posts in a structured response
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
            // Fetch the post with necessary relationships and counts
            $post = Post::with(['user', 'theme'])
                ->withCount(['comments', 'likes'])
                ->findOrFail($postId);

            // Format the response
            $response = [
                'id' => $post->id,
                'imageUrl' => $post->image_url,
                'description' => $post->description,
                'username' => $post->user->username,
                'userId' => $post->user->id,
                'themeName' => $post->theme->theme_name,
                'likes_count' => $post->likes_count,
                'comments_count' => $post->comments_count,
                'createdAt' => $post->created_at->toISOString(),
            ];

            return response()->json($response);
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
