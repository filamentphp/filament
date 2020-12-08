<?php

use Illuminate\Support\Facades\Route;
use Filament\Features;
use Filament\Http\Controllers\{
    AssetController,
    ResourceController,
    ImageController,
};

// Assets
Route::name('assets.')->group(function () {
    Route::get('filament.css', [AssetController::class, 'css'])->name('css');
    Route::get('filament.css.map', [AssetController::class, 'cssMap']);
    Route::get('filament.js', [AssetController::class, 'js'])->name('js');   
    Route::get('filament.js.map', [AssetController::class, 'jsMap']);  
});

// Images
Route::get('/image/{path}', ImageController::class)->where('path', '.*')->name('image');

// Authentication
Route::get('login', config('filament.livewire.login'))->name('login');
Route::get('forgot-password', config('filament.livewire.forgot-password'))->name('password.forgot');
Route::get('reset-password/{token}', config('filament.livewire.reset-password'))->name('password.reset');

// Registration
if (Features::registersUsers()) {
    Route::get('/register', config('filament.livewire.register'))->name('register');
}

// Authenticated routes
Route::group(['middleware' => ['auth.filament']], function () {
    // Dashboard
    if (Features::hasDashboard()) {
        Route::get('/', config('filament.livewire.dashboard'))->name('dashboard');
    }

    // Profile
    if (Features::hasUserProfile()) {
        Route::get('/profile', config('filament.livewire.profile'))->name('profile');
    }

    // Resources
    if (Features::hasResources()) {
        Route::get('/resources/{resource}/{action?}/{id?}', ResourceController::class)->name('resource');
    }
});