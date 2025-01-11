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
use App\Http\Middleware\LogCsrfTokens;

Route::get('/sanctum/csrf-cookie', function () {
    return response()->json();
});

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

// Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::put('/auth/update', [AuthController::class, 'update'])->name('auth.update');
    Route::delete('/auth/delete', [AuthController::class, 'destroy'])->name('auth.delete');


    // User Profile Routes
    Route::prefix('users')->group(function () {
        Route::post('/profile', [UserProfileController::class, 'store'])->name('profile.create');
        Route::get('/profile', [UserProfileController::class, 'index'])->name('profile.index');
        Route::get('/profile/{profileId}', [UserProfileController::class, 'show'])->name('profile.show');
        Route::delete('/profile/{profileId}', [UserProfileController::class, 'destroy'])->name('profile.destroy');
        Route::put('/profile', [UserProfileController::class, 'update'])->name('profile.update');
    });

    // Upload Routes
    Route::prefix('uploads')->group(function () {
        Route::post('/avatar', [UploadController::class, 'uploadAvatar'])->name('uploads.avatar');
        Route::post('/brand-logo', [UploadController::class, 'uploadBrandLogo'])->middleware('role:admin')->name('uploads.brand-logo');
        Route::get('/', [UploadController::class, 'index'])->name('uploads.index');
        Route::get('/{upload}', [UploadController::class, 'show'])->name('uploads.show');
        Route::post('/{upload}', [UploadController::class, 'update'])->name('uploads.update');
        Route::delete('{upload}', [UploadController::class, 'destroy'])->name('uploads.destroy');
    });

  // Artworks (Posts) Routes
    Route::prefix('artworks')->group(function () {
        Route::post('/', [PostController::class, 'store'])->name('artworks.store');
        Route::put('/{postId}', [PostController::class, 'update'])->name('artworks.update');
        Route::get('/', [PostController::class, 'index'])->name('artworks.index');
        Route::get('/{postId}', [PostController::class, 'show'])->name('artworks.show');
        Route::delete('/{postId}', [PostController::class, 'destroy'])->name('artworks.destroy');
    });

    // Theme Routes
    Route::prefix('themes')->group(function () {
        Route::get('/', [ThemeController::class, 'index'])->name('themes.index');
        Route::get('/{id}', [ThemeController::class, 'show'])->name('themes.show');
    });

    // Comments Routes
    Route::prefix('artworks/{postId}/comments')->group(function () {
        Route::post('/', [CommentController::class, 'store'])->name('comments.store');
        Route::put('/{commentId}', [CommentController::class, 'update'])->name('comments.update');
        Route::get('/', [CommentController::class, 'index'])->name('comments.index');
        Route::delete('/{commentId}', [CommentController::class, 'destroy'])->name('comments.destroy');
    });

    // Likes Routes
    Route::prefix('artworks/{postId}/likes')->group(function () {
        Route::post('/', [LikeController::class, 'store'])->name('likes.store');
        Route::get('/', [LikeController::class, 'index'])->name('likes.index');
        Route::delete('/{likeId}', [LikeController::class, 'destroy'])->name('likes.destroy');
    });

    // Sponsor Challenges and Submissions Routes
    Route::prefix('sponsor-challenges')->group(function () {
        // Sponsor Challenges Routes
        Route::get('/', [SponsorChallengeController::class, 'index'])->name('sponsor-challenges.index');
        Route::get('/{challengeId}', [SponsorChallengeController::class, 'show'])->name('sponsor-challenges.show');

        // Group sponsor submissions within sponsor challenges
        Route::prefix('{challengeId}/submissions')->group(function () {
            Route::get('/', [SponsorSubmissionController::class, 'index'])->name('sponsor-submissions.index');
            Route::get('/{submissionId}', [SponsorSubmissionController::class, 'show'])->name('sponsor-submissions.show');
            Route::post('/', [SponsorSubmissionController::class, 'store'])->middleware('role:pro')->name('sponsor-submissions.store');
            Route::put('/{submissionId}', [SponsorSubmissionController::class, 'update'])->middleware('role:pro')->name('sponsor-submissions.update');
            Route::delete('/{submissionId}', [SponsorSubmissionController::class, 'destroy'])->middleware('role:pro')->name('sponsor-submissions.destroy');

            // Voting Routes for Sponsor Submissions
            Route::prefix('{submissionId}/votes')->group(function () {
                Route::post('/', [VoteController::class, 'store'])->name('votes.store');
                Route::get('/', [VoteController::class, 'index'])->name('votes.index');
                Route::delete('/', [VoteController::class, 'destroy'])->name('votes.destroy');
            });
        });
    });

    // Archive Routes
    Route::prefix('archive')->group(function () {
        Route::post('/move', [ArchiveController::class, 'moveToArchive'])->middleware('role:admin')->name('archive.move'); // Only admin can move posts
        Route::get('/posts', [ArchiveController::class, 'viewArchivedPosts'])->name('archive.posts'); // Accessible to all users
    });


    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        // Sponsor Challenges Management
        Route::prefix('sponsor-challenges')->group(function () {
            Route::post('/', [SponsorChallengeController::class, 'store'])->name('admin.sponsor-challenges.store');
            Route::put('/{challengeId}', [SponsorChallengeController::class, 'update'])->name('admin.sponsor-challenges.update');
            Route::delete('/{challengeId}', [SponsorChallengeController::class, 'destroy'])->name('admin.sponsor-challenges.destroy');
        });

        // User Management
        Route::prefix('users')->group(function () {
            Route::post('/', [AdminUserController::class, 'create'])->name('admin.users.create');
            Route::get('/', [AdminUserController::class, 'index'])->name('admin.users.index');
            Route::put('/{userId}/role', [AdminUserController::class, 'updateRole'])->name('admin.users.update-role');
            Route::delete('/{userId}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
        });

        // Approvals and Themes
        Route::put('posts/{postId}/status', [AdminSubmissionController::class, 'updatePostStatus'])->name('admin.posts.update-status');
        Route::put('sponsor-submissions/{submissionId}/status', [AdminSubmissionController::class, 'updateSubmissionStatus'])->name('admin.sponsor-submissions.update-status');

        Route::get('pending-posts', [AdminSubmissionController::class, 'viewPendingPosts'])->name('admin.pending-posts');
        Route::get('pending-submissions', [AdminSubmissionController::class, 'viewPendingSubmissions'])->name('admin.pending-submissions');

        Route::post('themes', [ThemeController::class, 'store'])->name('admin.themes.store');
        Route::put('themes/{themeId}', [ThemeController::class, 'update'])->name('admin.themes.update');
        Route::delete('themes/{themeId}', [ThemeController::class, 'destroy'])->name('admin.themes.destroy');
    });
});
