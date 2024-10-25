<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    /**
     * Display a listing of the themes.
     */
    public function index(): JsonResponse
    {
        $themes = Theme::all();
        return response()->json($themes);
    }

    /**
     * Display the specified theme.
     */
    public function show($id): JsonResponse
    {
        $theme = Theme::findOrFail($id);
        return response()->json($theme);
    }

    /**
     * Store a newly created theme in storage (for future use).
     */
    public function store(Request $request): JsonResponse
    {
        // Return a message that themes can only be created by an admin
        return response()->json(['message' => 'Themes can only be created by admin.'], 403);
    }
}
