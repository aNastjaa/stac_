<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use App\Models\Post;
use App\Models\Archive;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ArchiveController extends Controller
{
    /**
     * Move posts associated with a theme to the archive.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function moveToArchive(Request $request): JsonResponse
    {
        // Validate the incoming request
        $request->validate([
            'theme_id' => 'required|uuid', // Ensure the theme_id is a valid UUID
        ]);
    
        // Find the theme based on the theme ID
        $theme = Theme::find($request->theme_id);
    
        if (!$theme) {
            return response()->json(['message' => 'Theme not found.'], 404);
        }
    
        try {
            // Move the theme itself to the archive (even if there are no posts)
            Archive::create([
                'theme_id' => $theme->id, // Assuming you store the theme in the archive
                'theme_name' => $theme->theme_name,
                'moved_at' => now(),
            ]);
    
            // Get all posts associated with this theme
            $posts = Post::where('theme_id', $theme->id)->get();
    
            // If there are posts, archive them and delete them
            foreach ($posts as $post) {
                Archive::create([
                    'post_id' => $post->id,
                    'moved_at' => now(),
                    'theme' => $theme->theme_name,
                ]);
                $post->delete(); // Delete the post after archiving
            }
    
            return response()->json(['message' => 'Theme archived successfully, including related posts if any.']);
    
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error archiving theme: ' . $e->getMessage());
    
            return response()->json(['message' => 'Error archiving theme: ' . $e->getMessage()], 500);
        }
    }
    
    

    /**
     * View all archived posts.
     *
     * @return JsonResponse
     */
    public function viewArchivedPosts(): JsonResponse
    {
        $archives = Archive::with('post')->get();

        return response()->json($archives, 200);
    }
}
