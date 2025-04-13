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
    Route::resource('users', Users\UsersController::class)->except(['edit', 'create']);
    Route::resource('subjects', Subjects\SubjectsController::class)->except(['edit', 'create']);
    Route::resource('subjects.highlights', Subjects\SubjectHighlightsController::class)->except(['edit', 'create']);
    Route::resource('subjects.skills', Subjects\SkillsController::class)->except(['edit', 'create']);
    Route::resource('subjects.employers', Subjects\EmployersController::class)->except(['edit', 'create']);
    Route::resource('subjects.employers.highlights', Subjects\EmployerHighlightsController::class)->except(['edit', 'create']);
    Route::resource('subjects.educations', Subjects\EducationsController::class)->except(['edit', 'create']);
    Route::resource('subjects.educations.highlights', Subjects\EducationHighlightsController::class)->except(['edit', 'create']);
});

Route::get('healthz', fn() => response()->json(['status' => 'ok']))->name('healthz');

Route::fallback(FallbackController::class);
