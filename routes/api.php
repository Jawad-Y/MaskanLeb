<?php

use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\ApartmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\JudiciaryController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\OwnerAnalyticsController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Public routes - no authentication required
| Protected routes - require auth:sanctum
| Admin routes - require admin role
|
*/

// ──────────────────────────────────────────────
//  Public Routes
// ──────────────────────────────────────────────

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Public apartment browsing
Route::get('apartments', [ApartmentController::class, 'index']);
Route::get('apartments/{apartment}', [ApartmentController::class, 'show']);

// Public judiciary listing
Route::get('judiciaries', [JudiciaryController::class, 'index']);
Route::get('judiciaries/{judiciary}/apartments', [JudiciaryController::class, 'apartments']);

// Public reviews
Route::get('apartments/{apartment}/reviews', [ReviewController::class, 'index']);
Route::get('owners/{owner}/reviews', [ReviewController::class, 'ownerReviews']);

// ──────────────────────────────────────────────
//  Authenticated Routes
// ──────────────────────────────────────────────

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/user', [AuthController::class, 'user']);

    // Profile
    Route::get('profile', [ProfileController::class, 'show']);
    Route::post('profile', [ProfileController::class, 'update']);
    Route::put('profile/password', [ProfileController::class, 'changePassword']);
    Route::delete('profile', [ProfileController::class, 'destroy']);

    // Apartments (owner actions)
    Route::post('apartments', [ApartmentController::class, 'store'])->middleware('owner');
    Route::put('apartments/{apartment}', [ApartmentController::class, 'update']);
    Route::delete('apartments/{apartment}', [ApartmentController::class, 'destroy']);
    Route::delete('apartments/{apartment}/images/{image}', [ApartmentController::class, 'deleteImage']);
    Route::get('my-listings', [ApartmentController::class, 'myListings']);

    // Favorites
    Route::get('favorites', [FavoriteController::class, 'index']);
    Route::post('apartments/{apartment}/favorite', [FavoriteController::class, 'toggle']);

    // Messages
    Route::get('messages/conversations', [MessageController::class, 'conversations']);
    Route::get('messages/unread-count', [MessageController::class, 'unreadCount']);
    Route::get('messages/{apartment}/{user}', [MessageController::class, 'show']);
    Route::post('messages', [MessageController::class, 'store']);

    // Reviews
    Route::post('reviews', [ReviewController::class, 'store']);
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy']);

    // Reports
    Route::post('reports', [ReportController::class, 'store']);
    Route::get('my-reports', [ReportController::class, 'myReports']);

    // Owner Analytics
    Route::middleware('owner')->prefix('analytics')->group(function () {
        Route::get('overview', [OwnerAnalyticsController::class, 'overview']);
        Route::get('apartments/{apartment}/views', [OwnerAnalyticsController::class, 'apartmentViews']);
    });
});

// ──────────────────────────────────────────────
//  Admin Routes
// ──────────────────────────────────────────────

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {

    // Dashboard
    Route::get('dashboard', [AdminController::class, 'dashboard']);

    // User management
    Route::get('users', [AdminController::class, 'users']);
    Route::post('users/{user}/ban', [AdminController::class, 'banUser']);
    Route::post('users/{user}/unban', [AdminController::class, 'unbanUser']);
    Route::post('users/{user}/verify', [AdminController::class, 'verifyUser']);
    Route::post('users/{user}/unverify', [AdminController::class, 'unverifyUser']);

    // Judiciary management
    Route::post('judiciaries', [AdminController::class, 'storeJudiciary']);
    Route::put('judiciaries/{judiciary}', [AdminController::class, 'updateJudiciary']);
    Route::delete('judiciaries/{judiciary}', [AdminController::class, 'destroyJudiciary']);

    // Apartment management
    Route::get('apartments', [AdminController::class, 'apartments']);
    Route::post('apartments/{apartment}/verify', [AdminController::class, 'verifyApartment']);
    Route::delete('apartments/{apartment}', [AdminController::class, 'removeApartment']);

    // Report management
    Route::get('reports', [AdminController::class, 'reports']);
    Route::put('reports/{report}', [AdminController::class, 'updateReport']);
});
