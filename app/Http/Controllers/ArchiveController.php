<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use App\Models\Post;
use App\Models\Archive;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            'theme' => 'required|string',
        ]);

        // Find the theme based on the theme name
        $theme = Theme::where('theme_name', $request->theme)->first();

        if (!$theme) {
            return response()->json(['message' => 'Theme not found.'], 404);
        }

        // Get all posts associated with this theme
        $posts = Post::where('theme_id', $theme->id)->get();

        // Check if there are any posts to archive
        if ($posts->isEmpty()) {
            return response()->json(['message' => 'No posts found for this theme.'], 404);
        }

        // Move each post to the archive
        foreach ($posts as $post) {
            Archive::create([
                'post_id' => $post->id,
                'moved_at' => now(),
                'theme' => $theme->theme_name,
            ]);
            // Optionally, you can decide to keep the posts in the original table or delete them
            $post->delete();
        }

        return response()->json(['message' => 'All related posts archived successfully for the theme.']);
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
