<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {

    //Register
    Route::view('/register','auth.register')->name('register');
    Route::post('/register',[AuthController::class, 'register']);

    //Log in
    Route::view('/login','auth.login')->name('login');
    Route::post('/login',[AuthController::class, 'login']);

});

Route::middleware('auth')->group(function () {

    //Logout
    Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

    //User profile
    Route::view('/profile','users.userprofile')->name('profile');
    Route::get('/profile', [UserProfileController::class, 'index'])->name('profile');

});
