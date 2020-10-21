<?php

use Filament\Features;

return [

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | By default all of Filament's features are enabled. You may enable 
    | or disable features as needed by your application.
    |
    */

    'features' => [
        Features::users(),
    ],

    'prefix' => [

        /*
        |--------------------------------------------------------------------------
        | Route Prefix
        |--------------------------------------------------------------------------
        |
        */

        'route' => 'filament',

        /*
        |--------------------------------------------------------------------------
        | Component Prefix
        |--------------------------------------------------------------------------
        |
        | If set with the default "filament", for example, you can reference components like:
        |
        | <x-filament-alert />
        | <x-filament::input />
        |
        */

        'component' => 'filament',
        
    ],

    /*
    |--------------------------------------------------------------------------
    | Components
    |--------------------------------------------------------------------------
    |
    | Below you reference all class-based components that should be loaded 
    | for your app.By default all components from Filament are loaded in. 
    | You can disable or overwrite any component class or alias that you want.
    |
    */

    'components' => [
        // 'alert' => Components\Alerts\Alert::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire Components
    |--------------------------------------------------------------------------
    |
    | Below you reference all the Livewire components that should be loaded
    | for your app. By default all components from Filament are loaded in.
    |
    */

    'livewire' => [
        'login' => Filament\Http\Livewire\Auth\Login::class,
        // 'forgot-password' => Filament\Http\Livewire\Auth\ForgotPassword::class,
        'dashboard' => Filament\Http\Livewire\Dashboard::class,
    ],

];
