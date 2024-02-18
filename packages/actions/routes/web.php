<?php

use Filament\Actions\Exports\Http\Controllers\DownloadExport;
use Filament\Actions\Imports\Http\Controllers\DownloadImportFailureCsv;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

foreach (Filament::getPanels() as $panel) {
    /** @var \Filament\Panel $panel */
    Route::get('/filament/exports/{export}/download', DownloadExport::class)
        ->name('filament.exports.download')
        ->middleware(array_merge($panel->getMiddleware(), $panel->getAuthMiddleware()));

    Route::get('/filament/imports/{import}/failed-rows/download', DownloadImportFailureCsv::class)
        ->name('filament.imports.failed-rows.download')
        ->middleware(array_merge($panel->getMiddleware(), $panel->getAuthMiddleware()));
}
