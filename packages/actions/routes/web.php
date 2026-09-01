<?php

use Filament\Actions\Exports\Http\Controllers\DownloadExport;
use Filament\Actions\Imports\Http\Controllers\DownloadImportFailureCsv;
use Illuminate\Support\Facades\Route;

$prefix = config('filament.system_route_prefix', 'filament');

Route::middleware('filament.actions')
    ->name('filament.')
    ->prefix($prefix)
    ->group(function (): void {
        Route::get('/exports/{export}/download', DownloadExport::class)
            ->name('exports.download');

        Route::get('/imports/{import}/failed-rows/download', DownloadImportFailureCsv::class)
            ->name('imports.failed-rows.download');
    });
