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
        Features::registration(),
        Features::dashboard(),
        Features::profile(),
        Features::resources(),
    ],

    'models' => [

        'user' => config('auth.providers.users.model'),
    
    ],

    'prefix' => [

        /*
        |--------------------------------------------------------------------------
        | Route Prefix
        |--------------------------------------------------------------------------
        |
        | The default is `admin` but you can change it to whatever works best and
        | doesn't conflict with the routing in your application.
        | 
        */

        'route' => 'admin',

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
    | Components
    |--------------------------------------------------------------------------
    |
    | Below you reference all class-based components use by Filament.
    | You may override these for customization in your own app.
    |
    */

    'components' => [
        'nav' => Filament\View\Components\Nav::class,
        'avatar' => Filament\View\Components\Avatar::class,
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
        'profile' => Filament\Http\Livewire\Profile::class,
        'account' => Filament\Http\Livewire\Account::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    |
    | This is the storage disk Filament will use to put media, you may
    | use any of the disks defined in the `config/filesystems.php`.
    |
    */
    
    'storage_disk' => env('FILAMENT_STORAGE_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | This is the cache disk Filament will use, you may
    | use any of the disks defined in the `config/filesystems.php`.
    |
    */

    'cache_disk' => env('FILAMENT_CACHE_DISK', 'local'),
    'cache_path_prefix' =>  'filament/cache',

];
