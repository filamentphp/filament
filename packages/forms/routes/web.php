<?php

use Filament\Forms\Http\Controllers\RequestPresignedUpload;
use Illuminate\Support\Facades\Route;

Route::post('/filament/uploads/request-presigned-upload', RequestPresignedUpload::class)
    ->name('filament.uploads.presigned-upload.request')
    ->middleware(['web', 'auth']);