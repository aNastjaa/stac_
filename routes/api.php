<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;
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


});
