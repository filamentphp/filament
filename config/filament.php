<?php

use Filament\Features;

return [

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | All of Filament's features are optional. You may enable the features
    | by adding / uncommenting items in this array as needed.
    |
    */

    'features' => [
         Features::dashboard(),
         Features::profile(),
         Features::resources(),
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | The default is `admin` but you can change it to whatever works best and
    | doesn't conflict with the routing in your application.
    |
    */

    'prefix' => 'admin',

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
        'avatar' => Filament\View\Components\Avatar::class,
        'image' => Filament\View\Components\Image::class,
        'nav' => Filament\View\Components\Nav::class,
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

    /*
    |--------------------------------------------------------------------------
    | Cache Path Prefix
    |--------------------------------------------------------------------------
    |
    | This is the cache path prefix used by Filament. It is relative to the
    | disk defined above.
    |
    */

    'cache_path_prefix' => 'filament/cache',

];
