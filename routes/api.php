<?php

use App\Http\Controllers\Auth;
use App\Http\Controllers\FallbackController;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', Auth\LoginController::class)->name('auth.login');

Route::middleware('auth:api')->group(function () {

    Route::group(['prefix' => 'auth'], function () {
        Route::get('user', Auth\UserController::class)->name('auth.user');
        Route::post('refresh', Auth\RefreshController::class)->name('auth.refresh');
        Route::post('logout', Auth\LogoutController::class)->name('auth.logout');
    });

    //users
    //subjects
    //subjects/{subject}/highlights
    //subjects/{subject}/skills
    //subjects/{subject}/employers
    //subjects/{subject}/employers/{employer}/highlights
    //subjects/{subject}/education
    //subjects/{subject}/education/{education}/highlights
});

Route::fallback(FallbackController::class);
