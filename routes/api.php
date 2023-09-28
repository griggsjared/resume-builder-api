<?php

use App\Http\Controllers\Auth;
use App\Http\Controllers\Subjects;
use App\Http\Controllers\Users;
use App\Http\Controllers\FallbackController;
use Illuminate\Support\Facades\Route;


Route::as('auth.')->group(function() {

    Route::post('auth/login', Auth\LoginController::class)->name('login');
    Route::post('auth/register', Auth\RegisterController::class)->name('register');

    Route::middleware('auth:api')->group(function () {
        Route::group(['prefix' => 'auth'], function () {
            Route::get('user', Auth\UserController::class)->name('user');
            Route::post('refresh', Auth\RefreshController::class)->name('refresh');
            Route::post('logout', Auth\LogoutController::class)->name('logout');
        });
    });
});

Route::middleware('auth:api')->group(function () {
    Route::resource('users', Users\UsersController::class)->except(['edit', 'create']);
    Route::resource('subjects', Subjects\SubjectsController::class)->except(['edit', 'create']);
//     Route::resource('subjects.highlights', SubjectHighlightController::class)->except(['show]);
//     Route::resource('subjects.skills', SubjectSkillController::class)->except(['show]);
//     Route::resource('subjects.employers', SubjectEmployerController::class)->except(['show]);
//     Route::resource('subjects.employers.highlights', SubjectEmployerHighlightController::class)->except(['show]);
//     Route::resource('subjects.education', SubjectEducationController::class)->except(['show]);
//     Route::resource('subjects.education.highlights', SubjectEducationHighlightController::class)->except(['show]);
});

Route::fallback(FallbackController::class);
