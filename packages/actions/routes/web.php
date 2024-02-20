<?php

use Filament\Actions\Exports\Http\Controllers\DownloadExport;
use Filament\Actions\Imports\Http\Controllers\DownloadImportFailureCsv;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

$guards = Collection::make(Filament::getPanels())
    ->map(static fn (Panel $panel): string => $panel->getAuthGuard())
    ->unique()
    ->implode(',');

Route::get('/filament/exports/{export}/download', DownloadExport::class)
    ->name('filament.exports.download')
    ->middleware(['web', "auth:$guards"]);

Route::get('/filament/imports/{import}/failed-rows/download', DownloadImportFailureCsv::class)
    ->name('filament.imports.failed-rows.download')
    ->middleware(['web', "auth:$guards"]);
