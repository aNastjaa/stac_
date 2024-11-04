<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\SponsorChallengeController;
use App\Http\Controllers\AdminUserController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;

// Existing Auth Routes
Route::post('/auth/register', [AuthController::class, 'register'])->name('register');
Route::post('/auth/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');

    // User Profile Routes
    Route::post('/users/profile', [UserProfileController::class, 'store'])->name('profile.create');
    Route::get('/users/profile', [UserProfileController::class, 'index'])->name('profile.show');
    Route::put('/users/profile', [UserProfileController::class, 'update'])->name('profile.update');

    // Uploads Routes
    Route::post('/uploads/avatar', [UploadController::class, 'uploadAvatar'])->name('uploads.avatar');
    Route::post('/uploads/brand-logo', [UploadController::class, 'uploadBrandLogo'])->name('uploads.brand-logo');
    Route::get('/uploads', [UploadController::class, 'index']);
    Route::put('/uploads/{id}', [UploadController::class, 'update'])->name('uploads.update');
    Route::delete('/uploads/{id}', [UploadController::class, 'destroy'])->name('uploads.destroy');

    // Artworks (Posts) Routes
    Route::post('/artworks', [PostController::class, 'store']);
    Route::put('/artworks/{id}', [PostController::class, 'update']);
    Route::get('/artworks', [PostController::class, 'index']);
    Route::get('/artworks/{id}', [PostController::class, 'show']);
    Route::delete('/artworks/{id}', [PostController::class, 'destroy']);

    // Themes Routes
    Route::get('/themes', [ThemeController::class, 'index']);
    Route::get('/themes/{id}', [ThemeController::class, 'show']);


    // Comments Routes
    Route::post('/artworks/{id}/comments', [CommentController::class, 'store']);
    Route::put('/comments/{id}', [CommentController::class, 'update']);
    Route::get('/artworks/{id}/comments', [CommentController::class, 'index']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

    // Likes Routes
    Route::post('/artworks/{id}/likes', [LikeController::class, 'store']);
    Route::get('/artworks/{id}/likes', [LikeController::class, 'index']);
    Route::delete('/likes/{id}', [LikeController::class, 'destroy']);

    // Sponsor Challenge Routes
    Route::get('/sponsor-challenges', [SponsorChallengeController::class, 'index']); // All users can view challenges
    Route::get('/sponsor-challenges/{id}', [SponsorChallengeController::class, 'show']); // View a specific challenge

    // Group for Admin Management
    Route::middleware([RoleMiddleware::class . ':admin'])->group(function () {
        Route::post('/admin/sponsor-challenges', [SponsorChallengeController::class, 'store']); // Admin creates new sponsor challenge
        Route::put('/admin/sponsor-challenges/{id}', [SponsorChallengeController::class, 'update']); // Admin updates sponsor challenge
        Route::delete('/admin/sponsor-challenges/{id}', [SponsorChallengeController::class, 'destroy']); // Admin deletes sponsor challenge

        Route::post('/admin/users', [AdminUserController::class, 'create']); // Create new admin user
        Route::get('/admin/users', [AdminUserController::class, 'index']); // Get all users
        Route::put('/admin/users/{id}/role', [AdminUserController::class, 'updateRole']); // Update user role
        Route::delete('/admin/users/{id}', [AdminUserController::class, 'destroy']); // Delete user

        Route::put('/admin/sponsor-submissions/{id}/status', [SponsorChallengeController::class, 'updateSubmissionStatus']); // Approve/Reject sponsor submissions
        Route::put('/admin/posts/{id}/status', [PostController::class, 'updatePostStatus']); // Approve/Reject user posts
        Route::post('/admin/themes', [ThemeController::class, 'store']);// Create new theme
    });
});

