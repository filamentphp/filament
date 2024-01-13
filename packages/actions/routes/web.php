<?php

use Filament\Actions\Exports\Http\Controllers\DownloadExport;
use Filament\Actions\Imports\Http\Controllers\DownloadImportFailureCsv;
use Illuminate\Support\Facades\Route;

Route::get('/filament/exports/{export}/download', DownloadExport::class)
    ->name('filament.exports.download')
    ->middleware(['web', 'auth']);

Route::get('/filament/imports/{import}/failed-rows/download', DownloadImportFailureCsv::class)
    ->name('filament.imports.failed-rows.download')
    ->middleware(['web', 'auth']);
