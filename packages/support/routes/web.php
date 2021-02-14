<?php

use Filament\Support\Http\Controllers;

Route::get('/assets/{filename}', Controllers\AssetController::class)->name('asset');
