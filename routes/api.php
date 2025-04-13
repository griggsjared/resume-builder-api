<?php

use App\Http\Controllers\Auth;
use App\Http\Controllers\FallbackController;
use App\Http\Controllers\Subjects;
use App\Http\Controllers\Users;
use Illuminate\Support\Facades\Route;

Route::as('auth.')->group(function () {
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

Route::middleware('auth:api')->scopeBindings()->group(function () {
    Route::apiResource('users', Users\UsersController::class);
    Route::apiResource('subjects', Subjects\SubjectsController::class);
    Route::apiResource('subjects.highlights', Subjects\SubjectHighlightsController::class);
    Route::apiResource('subjects.skills', Subjects\SkillsController::class);
    Route::apiResource('subjects.employers', Subjects\EmployersController::class);
    Route::apiResource('subjects.employers.highlights', Subjects\EmployerHighlightsController::class);
    Route::apiResource('subjects.educations', Subjects\EducationsController::class);
    Route::apiResource('subjects.educations.highlights', Subjects\EducationHighlightsController::class);
});

Route::get('healthz', fn() => response()->json(['status' => 'ok']))->name('healthz');

Route::fallback(FallbackController::class);
