<?php

use App\Http\Controllers\ListingController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Homepage - show all listings
Route::get('/', [ListingController::class, 'home'])->name('home');

// Authentication routes (Laravel Breeze or Jetstream will provide these)
// If you haven't installed auth yet, run: php artisan breeze:install
require __DIR__.'/auth.php';

// Public Listing Routes (anyone can view)
Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');

// Protected Listing Routes (must be logged in)
Route::middleware('auth')->group(function () {
    // Create new listing - MUST come BEFORE {slug} route
    Route::get('/listings/create', [ListingController::class, 'create'])->name('listings.create');
    Route::post('/listings', [ListingController::class, 'store'])->name('listings.store');
    
    // Package selection (after filling the form)
    Route::get('/listings/select-package', [ListingController::class, 'selectPackage'])->name('listings.select-package');
    Route::post('/listings/store-with-package', [ListingController::class, 'storeWithPackage'])->name('listings.store-with-package');
    
    // Edit/Update/Delete listings (user must own the listing)
    Route::get('/listings/{slug}/edit', [ListingController::class, 'edit'])->name('listings.edit');
    Route::put('/listings/{slug}', [ListingController::class, 'update'])->name('listings.update');
    Route::delete('/listings/{slug}', [ListingController::class, 'destroy'])->name('listings.destroy');
    
    // Delete single image from listing
    Route::delete('/listings/{slug}/images/{image}', [ListingController::class, 'deleteImage'])->name('listings.images.delete');
    
    // Favorite functionality
    Route::post('/listings/{id}/favorite', [UserDashboardController::class, 'toggleFavorite'])
         ->name('listings.favorite');
});

// Public listing detail - MUST come AFTER /listings/create
Route::get('/listings/{slug}', [ListingController::class, 'show'])->name('listings.show');

// User Dashboard Routes (must be logged in)
Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [UserDashboardController::class, 'index'])->name('index');
    Route::get('/my-listings', [UserDashboardController::class, 'myListings'])->name('my-listings');
    Route::get('/favorites', [UserDashboardController::class, 'favorites'])->name('favorites');
    
    // Profile management
    Route::get('/profile/edit', [UserDashboardController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [UserDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [UserDashboardController::class, 'updatePassword'])->name('profile.password');
});

// Admin Routes (must be logged in as admin)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    
    // Manage Listings
    Route::get('/listings', [AdminController::class, 'listings'])->name('listings');
    Route::post('/listings/{id}/approve', [AdminController::class, 'approveListing'])->name('listings.approve');
    Route::post('/listings/{id}/reject', [AdminController::class, 'rejectListing'])->name('listings.reject');
    Route::post('/listings/{id}/toggle-featured', [AdminController::class, 'toggleFeatured'])->name('listings.toggle-featured');
    Route::delete('/listings/{id}', [AdminController::class, 'deleteListing'])->name('listings.delete');
    
    // Manage Users
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{id}/toggle-active', [AdminController::class, 'toggleUserActive'])->name('users.toggle-active');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
    
    // Manage Categories
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{id}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminController::class, 'deleteCategory'])->name('categories.delete');

    // Package Management Routes
    Route::get('/packages', [AdminController::class, 'packages'])->name('packages');
    Route::post('/packages', [AdminController::class, 'storePackage'])->name('packages.store');
    Route::put('/packages/{id}', [AdminController::class, 'updatePackage'])->name('packages.update');
    Route::delete('/packages/{id}', [AdminController::class, 'deletePackage'])->name('packages.delete');
    Route::post('/packages/{id}/toggle-active', [AdminController::class, 'togglePackageActive'])->name('packages.toggle-active');
});