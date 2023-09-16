<?php

use App\Http\Controllers\Auth;
use App\Http\Controllers\FallbackController;
use Illuminate\Support\Facades\Route;

//Route::post('login', Auth\LoginController::class)->name('login');

Route::middleware('auth:api')->group(function () {

    Route::group(['prefix' => 'auth'], function () {
        Route::get('user', Auth\AuthorizedUserController::class)->name('authorized-user');
        //Route::post('logout', Auth\LogoutController::class)->name('logout');
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
