<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/test-register', function () {
    $user = new \App\Models\User();
    $user->username = 'TestUser2';
    $user->email = 'test2@example.com';
    $user->password = Hash::make('password');
    $user->role_id = 'e5734768-c6e5-42bc-82ba-8fff3b7fc2b1'; // Adjust as needed
    $user->save();
    return response()->json($user);
});

//Register
Route::view('/auth/register', 'auth.register')
->name('register');
Route::post('/auth/register', [AuthController::class, 'register'])
->name('register');

//Log in
Route::view('/auth/login', 'auth.login')
->name('login');
Route::post('/auth/login', [AuthController::class, 'login'])
->name('login');

//Group for auth middleware
 Route::middleware('auth:sanctum')->group(function () {
//Logout
    Route::post('/auth/logout',[AuthController::class, 'logout'])
    ->name('logout');

//User profile
    //Route::view('/users/userprofile', 'users.userprofile')
    //->name('profile');
    Route::post('/users/profile', [UserProfileController::class, 'store'])
    ->name('profile.create');
    Route::get('/users/profile', [UserProfileController::class, 'index']) // Fetch user profile
    ->name('profile.show');
    Route::put('/users/profile', [UserProfileController::class, 'update']) // Update user profile
    ->name('profile.update');

// Uploads
// ? ? Route::apiResource('uploads', UploadController::class)->only(['index']);
    Route::get('/uploads', [UploadController::class, 'index']);

// Specific routes for avatar and brand logo uploads
    Route::post('/uploads/avatar', [UploadController::class, 'uploadAvatar'])
    ->name('uploads.avatar');
    Route::post('/uploads/brand-logo', [UploadController::class, 'uploadBrandLogo'])
    ->name('uploads.brand-logo');

    Route::put('/uploads/{upload}', [UploadController::class, 'update'])
    ->name('uploads.update');
    Route::delete('/uploads/{upload}', [UploadController::class, 'destroy'])
    ->name('uploads.destroy');
});



