<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ThemeController extends Controller
{
    /**
     * Display a listing of the themes.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $themes = Theme::all();
        return response()->json($themes);
    }

    /**
     * Display the specified theme.
     *
     * @param  string  $themeId
     * @return JsonResponse
     */
    public function show(string $themeId): JsonResponse
    {
        $theme = Theme::findOrFail($themeId);
        return response()->json($theme);
    }

    /**
     * Store a newly created theme in storage (for future use).
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'theme_name' => 'required|string|max:255',
            'start_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create a new theme
        $theme = Theme::create([
            'theme_name' => $request->theme_name,
            'start_date' => $request->start_date,
        ]);

        return response()->json($theme, 201);
    }

    /**
     * Update the specified theme in storage.
     *
     * @param  Request  $request
     * @param  string  $themeId
     * @return JsonResponse
     */
    public function update(Request $request, string $themeId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'theme_name' => 'sometimes|required|string|max:255',
            'start_date' => 'sometimes|required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $theme = Theme::findOrFail($themeId);

        if ($request->has('theme_name')) {
            $theme->theme_name = $request->theme_name;
        }
        if ($request->has('start_date')) {
            $theme->start_date = $request->start_date;
        }

        $theme->save();

        return response()->json(['message' => 'Theme updated successfully.', 'theme' => $theme]);
    }

    /**
     * Remove the specified theme from storage.
     *
     * @param  string  $themeId
     * @return JsonResponse
     */
    public function destroy(string $themeId): JsonResponse
    {
        $theme = Theme::findOrFail($themeId);
        $theme->delete();

        return response()->json(['message' => 'Theme deleted successfully.']);
    }
}
