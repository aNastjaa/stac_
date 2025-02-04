<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use App\Models\Post;
use App\Models\Archive;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                'theme_id' => $theme->id, // Store the theme_id in the archive
                'theme_name' => $theme->theme_name, // Store the theme_name in the archive
                'moved_at' => now(),
            ]);
    
            // Get all posts associated with this theme
            $posts = Post::where('theme_id', $theme->id)->get();
    
            // If there are posts, archive them and delete them
            foreach ($posts as $post) {
                Archive::create([
                    'post_id' => $post->id, // Store the post_id in the archive
                    'theme_id' => $theme->id, // Store the theme_id in the archive for the post
                    'theme_name' => $theme->theme_name, // Store the theme_name in the archive for the post
                    'moved_at' => now(),
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
        // Fetch archived posts along with related post details
        $archives = Archive::with('post')->get();

        return response()->json($archives, 200);
    }

    
    public function viewArchivedThemes()
{
    // Fetch the archived themes from the 'archives' table
    $archivedThemes = DB::table('archives')
                        ->join('themes', 'archives.theme_id', '=', 'themes.id')
                        ->select('themes.*') 
                        ->get();

    return response()->json($archivedThemes);
}

}

