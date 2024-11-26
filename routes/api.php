<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\PreferenceController;

// Authentication Routes
Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register'); // Register a new user
    Route::post('/login', 'login');       // Log in a user
    Route::post('/logout', 'logout')->middleware('auth:sanctum'); // Log out the authenticated user
    Route::post('/password/email', 'sendResetLinkEmail');        // Send password reset link
    Route::post('/password/reset', 'resetPassword');            // Reset the password
});

// Authenticated Routes (Protected by sanctum middleware)
Route::middleware('auth:sanctum')->group(function () {
    // Article Routes
    Route::controller(ArticleController::class)->group(function () {
        Route::get('/article/filters', 'fetchDataForFiltersFromArticle'); // Fetch filter options
        Route::get('/articles', 'index');                                // List all articles
        Route::get('/articles/show/{id}', 'show');                       // Show the article
    });

    // Preference Routes
    Route::controller(PreferenceController::class)->group(function () {
        Route::post('/preferences', 'setPreferences');     // Set user preferences
        Route::get('/preferences', 'getPreferences');      // Retrieve user preferences
        Route::get('/preferences/feed', 'fetchPersonalizedFeed'); // Fetch personalized feed
    });
});