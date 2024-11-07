<?php

use App\Http\Controllers\AdminSubmissionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\SponsorChallengeController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\SponsorSubmissionController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\ArchiveController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

// Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');

    // User Profile Routes
    Route::prefix('users')->group(function () {
        Route::post('/profile', [UserProfileController::class, 'store'])->name('profile.create');
        Route::get('/profile', [UserProfileController::class, 'index'])->name('profile.show');
        Route::put('/profile', [UserProfileController::class, 'update'])->name('profile.update');
    });

    // Upload Routes
    Route::prefix('uploads')->group(function () {
        Route::post('/avatar', [UploadController::class, 'uploadAvatar'])->name('uploads.avatar');
        Route::post('/brand-logo', [UploadController::class, 'uploadBrandLogo'])->name('uploads.brand-logo');
        Route::get('/', [UploadController::class, 'index']);
        Route::get('/{id}', [UploadController::class, 'show']);
        Route::post('/{id}', [UploadController::class, 'update'])->name('uploads.update');
        Route::delete('/{id}', [UploadController::class, 'destroy'])->name('uploads.destroy');
    });

    // Artworks (Posts) Routes
    Route::prefix('artworks')->group(function () {
        Route::post('/', [PostController::class, 'store']);
        Route::put('/{id}', [PostController::class, 'update']);
        Route::get('/', [PostController::class, 'index']);
        Route::get('/{id}', [PostController::class, 'show']);
        Route::delete('/{id}', [PostController::class, 'destroy']);
    });

    // Theme Routes
    Route::prefix('themes')->group(function () {
        Route::get('/', [ThemeController::class, 'index']);
        Route::get('/{id}', [ThemeController::class, 'show']);
    });

    // Comments Routes
    Route::prefix('artworks/{id}/comments')->group(function () {
        Route::post('/', [CommentController::class, 'store']);
        Route::put('/{commentId}', [CommentController::class, 'update']);
        Route::get('/', [CommentController::class, 'index']);
        Route::delete('/{commentId}', [CommentController::class, 'destroy']);
    });

    // Likes Routes
    Route::prefix('artworks/{id}/likes')->group(function () {
        Route::post('/', [LikeController::class, 'store']);
        Route::get('/', [LikeController::class, 'index']);
        Route::delete('/{likeId}', [LikeController::class, 'destroy']);
    });

    // Sponsor Challenges and Submissions Routes
    Route::prefix('sponsor-challenges')->group(function () {
        Route::get('/', [SponsorChallengeController::class, 'index']);
        Route::get('/{id}', [SponsorChallengeController::class, 'show']);

        // Group sponsor submissions within sponsor challenges
        Route::prefix('{challengeId}/submissions')->group(function () {
            Route::get('/', [SponsorSubmissionController::class, 'index']);
            Route::get('/{submissionId}', [SponsorSubmissionController::class, 'show']);
            Route::post('/', [SponsorSubmissionController::class, 'store'])->middleware('role:pro');
            Route::put('/{submissionId}', [SponsorSubmissionController::class, 'update'])->middleware('role:pro');
            Route::delete('/{submissionId}', [SponsorSubmissionController::class, 'destroy'])->middleware('role:pro');

            // Voting Routes for Sponsor Submissions
            Route::prefix('{submissionId}/votes')->group(function () {
                Route::post('/', [VoteController::class, 'store'])->name('votes.store');
                Route::get('/', [VoteController::class, 'index'])->name('votes.index');
                Route::delete('/', [VoteController::class, 'destroy'])->name('votes.destroy');
            });
        });
    });

    Route::prefix('archive')->group(function () {
        Route::post('/move', [ArchiveController::class, 'moveToArchive'])->middleware('role:admin'); // Only admin can move posts
        Route::get('/posts', [ArchiveController::class, 'viewArchivedPosts']); // Accessible to all users
    });

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        // Sponsor Challenges Management
        Route::prefix('sponsor-challenges')->group(function () {
            Route::post('/', [SponsorChallengeController::class, 'store']);
            Route::put('/{id}', [SponsorChallengeController::class, 'update']);
            Route::delete('/{id}', [SponsorChallengeController::class, 'destroy']);
        });

        // User Management
        Route::prefix('users')->group(function () {
            Route::post('/', [AdminUserController::class, 'create']);
            Route::get('/', [AdminUserController::class, 'index']);
            Route::put('/{id}/role', [AdminUserController::class, 'updateRole']);
            Route::delete('/{id}', [AdminUserController::class, 'destroy']);
        });

    // Approvals and Themes
    Route::put('posts/{id}/status', [AdminSubmissionController::class, 'updatePostStatus']);
    Route::put('sponsor-submissions/{id}/status', [AdminSubmissionController::class, 'updateSubmissionStatus']);

    Route::get('pending-posts', [AdminSubmissionController::class, 'viewPendingPosts']);
    Route::get('pending-submissions', [AdminSubmissionController::class, 'viewPendingSubmissions']);

    Route::post('themes', [ThemeController::class, 'store']);
    Route::put('themes/{id}', [ThemeController::class, 'update']);
    Route::delete('themes/{id}', [ThemeController::class, 'destroy']);

    });
});
