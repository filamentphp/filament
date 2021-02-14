<?php

use Filament\Http\Controllers;
use Filament\Http\Livewire;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\RedirectIfAuthenticated;
use Filament\Models\Resource;
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
    Route::get('/account', Livewire\EditAccount::class)->name('account');

    Route::post('/rich-editor-attachments', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'file' => ['required', 'file'],
        ]);

        $directory = $request->input('directory', 'attachments');
        $disk = $request->input('disk', config('filament.default_filesystem_disk'));

        $path = $request->file('file')->store($directory, $disk);

        return \Illuminate\Support\Facades\Storage::disk($disk)->url($path);
    })->name('rich-editor-attachments.upload');

    // Resources
    Resource::all()->each(function ($resource) {
        foreach ($resource->router()->routes as $route) {
            Route::get('/resources/' . $resource->slug . '/' . $route->uri, $route->action)
                ->middleware('filament.authorize.resource-route:' . $resource->id . ',' . $route->name)
                ->name('resources.' . $resource->slug . '.' . $route->name);
        }
    });
});
