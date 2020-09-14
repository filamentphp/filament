<?php

use Filament\Features;

return [
    
    /*
    |--------------------------------------------------------------------------
    | Filament Stack
    |--------------------------------------------------------------------------
    |
    | This configuration value informs Filament which "stack" you will be
    | using for Jetstream. Livewire is currently the only available 
    | stack for this package.
    |
    */

    'stack' => 'livewire',

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | By default all of Filament's features are optional. You may enable
    | features as needed by your application.
    |
    */

    'features' => [
        Features::managesUsers(),
    ],

];
