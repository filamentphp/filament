<?php

use Filament\Facades\Filament;
use Filament\Http\Controllers\AssetController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

Route::domain(config('filament.domain'))
    ->middleware(config('filament.middleware'))
    ->name('filament.')
    ->prefix(config('filament.path'))
    ->group(function () {
        Route::name('pages.')->group(function () {
            foreach (Filament::getPages() as $page) {
                Route::group([], $page::getRoutes());
            }
        });

        Route::name('resources.')->group(function () {
            foreach (Filament::getResources() as $resource) {
                Route::group([], $resource::getRoutes());
            }
        });

        Route::get('/assets/{path}', AssetController::class)->name('asset');

        Route::get('/logout', function (): RedirectResponse {
            auth()->logout();

            return redirect()->route('login');
        })->name('logout');
    });
