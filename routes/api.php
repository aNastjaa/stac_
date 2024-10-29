<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\SponsorChallengeController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

//Register
// Route::view('/auth/register', 'auth.register')
// ->name('register');
Route::post('/auth/register', [AuthController::class, 'register'])
->name('register');

//Log in
// Route::view('/auth/login', 'auth.login')
// ->name('login');
Route::post('/auth/login', [AuthController::class, 'login'])
->name('login');

//Group for auth middleware
Route::middleware('auth:sanctum')->group(function () {
//Logout
    Route::post('/auth/logout',[AuthController::class, 'logout'])
    ->name('logout');

//User profile
    // Route::view('/users/userprofile', 'users.userprofile')
    // ->name('profile');
    Route::post('/users/profile', [UserProfileController::class, 'store'])
    ->name('profile.create');
    Route::get('/users/profile', [UserProfileController::class, 'index'])
    ->name('profile.show');
    Route::put('/users/profile', [UserProfileController::class, 'update'])
    ->name('profile.update');

// Uploads
// ? ? Route::apiResource('uploads', UploadController::class)->only(['index']);
    Route::post('/uploads/avatar', [UploadController::class, 'uploadAvatar'])
    ->name('uploads.avatar');
    Route::post('/uploads/brand-logo', [UploadController::class, 'uploadBrandLogo'])
    ->name('uploads.brand-logo');

    Route::get('/uploads', [UploadController::class, 'index']);
    Route::put('/uploads/{id}', [UploadController::class, 'update'])
    ->name('uploads.update');
    Route::delete('/uploads/{id}', [UploadController::class, 'destroy'])
    ->name('uploads.destroy');

//Posts (artworks)
    Route::post('/artworks', [PostController::class, 'store']);

    Route::put('/artworks/{id}', [PostController::class, 'update']);
    Route::get('/artworks', [PostController::class, 'index']);
    Route::get('/artworks/{id}', [PostController::class, 'show']);
    Route::delete('/artworks/{id}', [PostController::class, 'destroy']);

// Themes(test routing, in future admin access only)
    Route::get('/themes', [ThemeController::class, 'index']);
    Route::get('/themes/{id}', [ThemeController::class, 'show']);
    Route::post('/themes', [ThemeController::class, 'store']); // 403

//Comments system
    Route::post('/artworks/{id}/comments', [CommentController::class, 'store']);

    Route::put('/comments/{id}', [CommentController::class, 'update']);
    Route::get('/artworks/{id}/comments', [CommentController::class, 'index']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

//Likes system
    Route::post('/artworks/{id}/likes', [LikeController::class, 'store']);
    Route::get('/artworks/{id}/likes', [LikeController::class, 'index']);
    Route::delete('/likes/{id}', [LikeController::class, 'destroy']);

    //->middleware(RoleMiddleware::class . ':pro'); // Only Pro users can submit
    Route::post('/admin/sponsor-challenges', [SponsorChallengeController::class, 'store']); // Create
//Group for admin middleware
    Route::middleware([RoleMiddleware::class . ':admin'])->group(function () {

        Route::put('/admin/sponsor-challenges/{id}', [SponsorChallengeController::class, 'update']); // Update
        Route::delete('/admin/sponsor-challenges/{id}', [SponsorChallengeController::class, 'destroy']); // Delete
    });

// Non-Admin Sponsor Challenge Routes
     Route::get('/sponsor-challenges', [SponsorChallengeController::class, 'index']); // List all challenges
     Route::get('/sponsor-challenges/{id}', [SponsorChallengeController::class, 'show']); // View a specific challenge
});
