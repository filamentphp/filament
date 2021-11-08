<?php

use Filament\Facades\Filament;
use Filament\Http\Controllers\AssetController;
use Filament\Http\Livewire;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

Route::domain(config('filament.domain'))
    ->middleware(config('filament.middleware'))
    ->name('filament.')
    ->prefix(config('filament.path'))
    ->group(function () {
        Route::get('/', Livewire\Dashboard::class)->name('dashboard');

        foreach (Filament::getResources() as $resource) {
            $resource::registerRoutes();
        }

        foreach (Filament::getPages() as $page) {
            $page::registerRoutes();
        }

        Route::get('/assets/{path}', AssetController::class)->name('asset');

        Route::get('/logout', function (): RedirectResponse {
            auth()->logout();

            return redirect()->route('login');
        })->name('logout');
    });
