<?php

use Filament\Actions\Imports\Http\Controllers\DownloadImportFailureCsv;
use Illuminate\Support\Facades\Route;

Route::get('/filament/imports/{import}/failed-rows/download', DownloadImportFailureCsv::class)
    ->name('filament.imports.failed-rows.download')
    ->middleware(['web', 'auth']);
