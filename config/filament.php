<?php

use Filament\Features;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of the Filament application. This value is used 
    | when the package needs to place the application's name in a notification 
    | or any other location as required by the package.
    |
    */

    'name' => env('FILAMENT_NAME', 'Filament'),

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
        Features::registration(),
        Features::resourceModels()
    ],

    'models' => [

        'navigation' => Filament\Models\Navigation::class,

        'user' => config('auth.providers.users.model'),
    
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
        | If set with the default "filament", for example, 
        | you can reference components like:
        |
        | <x-filament-alert />
        | <x-filament::input />
        |
        */

        'component' => 'filament',
        
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    |
    | Navigations used by Filament.
    |
    */

    'nav' => [

        [
            'path' => 'filament.dashboard', // string or named route
            'active' => 'filament.dashboard', // string or named route (may also be an array @link https://github.com/dwightwatson/active)
            'label' => 'Dashboard',
            'icon' => 'heroicon-o-home', // optional icon name (Blade UI Kit @link https://github.com/blade-ui-kit/blade-heroicons)
        ],
    
    ],

    /*
    |--------------------------------------------------------------------------
    | Components
    |--------------------------------------------------------------------------
    |
    | Below you reference all class-based components use by Filament.
    | You may override these components for customization in your own app.
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
    | Below you reference all the required Livewire components used by Filament. 
    | You may override these components for customization in your own app.
    |
    */

    'livewire' => [
        'login' => Filament\Http\Livewire\Auth\Login::class,
        'forgot-password' => Filament\Http\Livewire\Auth\ForgotPassword::class,
        'reset-password' => Filament\Http\Livewire\Auth\ResetPassword::class,
        'register' => Filament\Http\Livewire\Auth\Register::class,
        'logout' => Filament\Http\Livewire\Auth\Logout::class,
        'dashboard' => Filament\Http\Livewire\Dashboard::class,
    ],

];
