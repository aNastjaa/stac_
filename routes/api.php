<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Log;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

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
 //Route::middleware('auth:sanctum')->group(function () {

    //Logout
    Route::post('/auth/logout',[AuthController::class, 'logout'])
    ->name('logout');

    //User profile
    Route::view('/users/userprofile', 'users.userprofile')
    ->name('profile');
    Route::get('/users/userprofile', [UserProfileController::class, 'index'])
    ->name('api.profile');
  //Route::put('/users/profile', [UserProfileController::class, 'updateProfile']);

// Uploads
//Route::apiResource('uploads', UploadController::class)->only(['index']);


    // Specific routes for avatar and brand logo uploads
    Route::get('/uploads', [UploadController::class, 'index']);

    Route::post('/uploads/avatar', [UploadController::class, 'uploadAvatar'])
    ->name('uploads.avatar');
    Route::post('/uploads/brand-logo', [UploadController::class, 'uploadBrandLogo'])
    ->name('uploads.brand-logo');

    Route::put('/uploads/{upload}', [UploadController::class, 'update'])
    ->name('uploads.update');
    Route::delete('/uploads/{upload}', [UploadController::class, 'destroy'])
    ->name('uploads.destroy');


//});



