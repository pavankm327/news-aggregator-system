<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\PreferenceController;

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
    Route::post('/password/email', 'sendResetLinkEmail');
    Route::post('/password/reset', 'resetPassword');
});

Route::middleware('auth:sanctum')->group( function () {
    Route::controller(ArticleController::class)->group(function () {
        Route::get('/article/filters', 'fetchDataForFiltersFromArticle');
        Route::get('/articles', 'index');
    });
    Route::controller(PreferenceController::class)->group(function () {
        Route::post('/preferences', 'setPreferences');
        Route::get('/preferences', 'getPreferences');
        Route::get('/preferences/feed', 'fetchPersonalizedFeed');
    });
});