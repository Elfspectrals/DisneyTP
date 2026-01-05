<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\StreamingProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MovieController;
use App\Http\Controllers\Admin\SeriesController;
use App\Http\Controllers\Admin\GenreController;
use App\Http\Controllers\Admin\ActorController;
use App\Http\Controllers\Admin\DirectorController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public catalog routes (accessible to everyone)
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/movies', [CatalogController::class, 'movies'])->name('movies');
Route::get('/series', [CatalogController::class, 'series'])->name('series');
Route::get('/content/{slug}', [ContentController::class, 'show'])->name('content.show');

// Auth routes (from Breeze)
require __DIR__.'/auth.php';

// Authenticated routes
Route::middleware(['auth', 'profile'])->group(function () {
    // User profile management (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Dashboard
    Route::get('/dashboard', function () {
        return redirect()->route('catalog');
    })->name('dashboard');
    
    // Streaming profiles (multi-profile management)
    Route::resource('profiles', StreamingProfileController::class);
    Route::post('/profiles/{profile}/switch', [StreamingProfileController::class, 'switch'])->name('profiles.switch');
    
    // Watchlist
    Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist');
    Route::post('/watchlist/{type}/{id}', [WatchlistController::class, 'add'])->name('watchlist.add');
    Route::delete('/watchlist/{type}/{id}', [WatchlistController::class, 'remove'])->name('watchlist.remove');
    
    // Content interaction
    Route::post('/content/{slug}/rate', [ContentController::class, 'rate'])->name('content.rate');
    Route::post('/content/{slug}/review', [ContentController::class, 'review'])->name('content.review');
    Route::post('/content/{slug}/watch', [ContentController::class, 'updateWatchProgress'])->name('content.watch');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Movies management
    Route::resource('movies', MovieController::class);
    
    // Series management
    Route::resource('series', SeriesController::class);
    Route::get('/series/{series}/episodes', [SeriesController::class, 'episodes'])->name('series.episodes');
    Route::post('/series/{series}/episodes', [SeriesController::class, 'storeEpisode'])->name('series.episodes.store');
    
    // Genres management
    Route::resource('genres', GenreController::class);
    
    // Actors management
    Route::resource('actors', ActorController::class);
    
    // Directors management
    Route::resource('directors', DirectorController::class);
});
