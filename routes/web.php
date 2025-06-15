<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // News Routes
    Route::resource('news', NewsController::class);
    Route::post('news/{news}/publish', [NewsController::class, 'publish'])->name('news.publish');
    Route::post('news/{news}/unpublish', [NewsController::class, 'unpublish'])->name('news.unpublish');

    // Category Routes
    Route::resource('categories', CategoryController::class);

    // Social Auth Routes
    Route::get('auth/{provider}', [App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToProvider'])
        ->name('social.auth');
    Route::get('auth/{provider}/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'handleProviderCallback'])
        ->name('social.callback');
});

require __DIR__.'/auth.php';
