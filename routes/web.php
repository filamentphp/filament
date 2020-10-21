<?php

use Illuminate\Support\Facades\Route;
use Filament\Features;
use Filament\Http\Controllers\{
    AssetController,
};

Route::group([
    'prefix' => config('filament.prefix.route'),
    'middleware' => config('filament.middleware', ['web']),
    'as' => 'filament.',
], function () {
    // Assets
    Route::name('assets.')->group(function () {
        Route::get('filament.css', [AssetController::class, 'css'])->name('css');
        Route::get('filament.css.map', [AssetController::class, 'cssMap']);
        Route::get('filament.js', [AssetController::class, 'js'])->name('js');   
        Route::get('filament.js.map', [AssetController::class, 'jsMap']);  
    });

    // Auth
    Route::get('/login', config('filament.livewire.login'))->name('login');
    // Route::get('/forgot-password', config('filament.livewire.forgot-password'))->name('password.forgot');

    // Authenticated routes
    Route::group(['middleware' => ['auth', 'verified']], function () {
        // Dashboard
        Route::get('/', config('filament.livewire.dashboard'))->name('dashboard');

        /*
        // User profile
        Route::get('/profile', config('filament.livewire.profile'))->name('profile');

        // Users
        if (Features::managesUsers()) {
            Route::name('users.')->group(function () {
                Route::get('/users', config('filament.livewire.users'))->name('index');
                Route::get('/user/{user}', config('filament.livewire.user'))->name('show');
            });
        }
        */
    });
});

// Conditional route login alias
if (!Route::has('login')) {
    Route::get('/login', function () {
        return redirect()->route("filament.login");
    })->name('login');
}