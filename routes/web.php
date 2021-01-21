<?php

use Filament\Http\Controllers;
use Filament\Http\Livewire;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Support\Facades\Route;

// Assets
Route::name('assets.')->group(function () {
    Route::get('filament.css', [Controllers\AssetController::class, 'css'])->name('css');
    Route::get('filament.css.map', [Controllers\AssetController::class, 'cssMap']);
    Route::get('filament.js', [Controllers\AssetController::class, 'js'])->name('js');
    Route::get('filament.js.map', [Controllers\AssetController::class, 'jsMap']);
});

// Authentication
Route::middleware([RedirectIfAuthenticated::class])->name('auth.')->group(function () {
    Route::get('login', Livewire\Auth\Login::class)->name('login');
    Route::get('forgot-password', Livewire\Auth\RequestPassword::class)->name('password.request');
    Route::get('reset-password/{token}', Livewire\Auth\ResetPassword::class)->middleware([ValidateSignature::class])->name('password.reset');
});

// Images
Route::get('/image/{path}', Controllers\ImageController::class)->where('path', '.*')->name('image');

// Authenticated routes
Route::middleware([Authenticate::class])->group(function () {
    Route::get('/', Livewire\Dashboard::class)->name('dashboard');
    Route::get('/account', Livewire\UpdateAccountForm::class)->name('account');

    // Resources
    \Filament\Filament::resources()->each(function ($resource) {
        collect($resource::actions())->each(function ($action, $route) use ($resource) {
            Route::get('/resources/' . $resource::getSlug() . '/' . $route, $action)->name('resources.' . $resource::getSlug() . '.' . $action);
        });
    });
});
