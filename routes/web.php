<?php

use Filament\Http\Controllers;
use Filament\Http\Livewire;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\RedirectIfAuthenticated;
use Filament\Models\Resource;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Support\Facades\Route;

Route::get('assets/{path}', Controllers\AssetController::class)->where('path', '.*')->name('asset');

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
    Route::get('/account', Livewire\EditAccount::class)->name('account');

    Route::post('/rich-editor-attachments', \Filament\Forms\Http\Controllers\RichEditorAttachmentController::class)->name('rich-editor-attachments.upload');

    // Resources
    Resource::all()->each(function ($resource) {
        foreach ($resource->router()->routes as $route) {
            Route::get('/resources/' . $resource->slug . '/' . $route->uri, $route->action)
                ->middleware('filament.authorize.resource-route:' . $resource->id . ',' . $route->name)
                ->name('resources.' . $resource->slug . '.' . $route->name);
        }
    });
});
