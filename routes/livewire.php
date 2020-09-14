<?php

use Illuminate\Support\Facades\Route;
use Laravel\Jetstream\Jetstream;

Route::group(['middleware' => config('filament.middleware.web', ['web'])], function () {
    Route::group(['middleware' => ['auth', 'verified']], function () {
        // API...
        if (Jetstream::hasApiFeatures()) {

        }
        
        // Teams...
        if (Jetstream::hasTeamFeatures()) {

        }
    });
});