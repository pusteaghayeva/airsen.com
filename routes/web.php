<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

// Public Landing Page & Localization
Route::get('/', [LandingController::class, 'index'])->name('landing.index');
Route::get('/login', fn () => redirect()->route('admin.login.view'))->name('login');
Route::get('/{locale}', [LandingController::class, 'index'])
    ->where('locale', 'az|ru|en')
    ->name('landing.locale');

// Async Analytics & Reviews
Route::post('/api/track', [AnalyticsController::class, 'track'])->middleware('throttle:analytics')->name('analytics.track');
Route::post('/api/reviews/submit', [ReviewController::class, 'store'])->middleware('throttle:public-forms')->name('reviews.store');
Route::post('/api/beta-notify', [ReviewController::class, 'subscribeBeta'])->middleware('throttle:public-forms')->name('beta.notify');

// Admin Panel & Analytics Dashboard
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/login', [AdminController::class, 'index'])->name('login.view');
    Route::post('/login', [AdminController::class, 'login'])->middleware('throttle:admin-login')->name('login');

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
        Route::post('/reviews/{id}/approve', [AdminController::class, 'approveReview'])->name('reviews.approve');
        Route::post('/reviews/{id}/delete', [AdminController::class, 'deleteReview'])->name('reviews.delete');

        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::post('/users/{id}/update', [AdminController::class, 'updateUser'])->name('users.update');
        Route::post('/users/{id}/delete', [AdminController::class, 'deleteUser'])->name('users.delete');

        Route::post('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
        Route::post('/settings/google-play', [AdminController::class, 'updateGooglePlay'])->name('settings.google-play');

        Route::post('/beta/delete', [AdminController::class, 'deleteBetaSubscriber'])->name('beta.delete');
        Route::post('/beta/clear', [AdminController::class, 'clearBetaSubscribers'])->name('beta.clear');
    });
});
