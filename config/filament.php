<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Name
    |--------------------------------------------------------------------------
    |
    | This is the name used by Filament.
    |
    */

    'name' => env('FILAMENT_NAME', 'Filament'),

    /*
    |--------------------------------------------------------------------------
    | Base Route Prefix
    |--------------------------------------------------------------------------
    |
    | This is the URI path where Filament will be accessible from.
    |
    */

    'path' => env('FILAMENT_PATH_NAME', 'filament'),

    /*
    |--------------------------------------------------------------------------
    | Namespaces
    |--------------------------------------------------------------------------
    |
    | These are the default namespaces where Filament looks for classes to
    | extend functionality. You may pass in either a string
    | or an array, they are tried in order and the first match is used.
    |
    */

    'namespaces' => [
        'fieldsets' => ['App\\Filament\\Fieldsets', 'Filament\\Fieldsets'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Redirects
    |--------------------------------------------------------------------------
    |
    | This is the URI path where Filament will be accessible from.
    |
    */

    'redirects' => [
        'admin' => 'filament.admin.dashboard', // string or named route to redirect users when successfully authenticated.
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

        'admin' => [

            'dashboard' => [
                'label' => 'filament::admin.dashboard',
                'icon' => 'heroicons/heroicon-o-home', // path to SVG icon using Filament::icon() SVG icon helper
                'url' => 'filament.admin.dashboard', // string or named route
                'active' => 'filament.admin.dashboard', // string or named route (may also be an array @link https://github.com/dwightwatson/active)
            ],

            /* TODO

            'Resources' => [
                'label' => 'filament::admin.resources',
                'icon' => 'heroicons/heroicon-o-collection',
                'url' => '#',
            ],

            'globals' => [
                'label' => 'filament::admin.globals',
                'icon' => 'heroicons/heroicon-o-globe',
                'url' => '#',
            ],

            */

            'users' => [
                'label' => 'filament::admin.users',
                'icon' => 'heroicons/heroicon-o-user-group',
                'url' => 'filament.admin.users.index',
                'active' => 'filament.admin.users.*',
                'ability' => 'view users',
            ],

            /* TODO 

            'Permissions' => [
                'label' => 'filament::admin.permissions',
                'icon' => 'heroicons/heroicon-o-lock-closed',
                'url' => 'filament.admin.permissions.index',
                'active' => 'filament.admin.permissions.*',
                'ability' => 'view permissions',
            ],

            */

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | The Eloquent Models used by Filament.
    |
    */
    
    'models' => [
        // 'resource' => Filament\Models\Resource::class,
    ],    

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will be attached to every route type in Filament.
    |
    */
    
    'middleware' => [
        'web' => [
            'web',
        ],
        'api' => [
            'api',
        ],
    ],

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
    'cache_path' =>  '.cache',

    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    |
    | This is the storage disk Filament will use to put file uploads, you may
    | use any of the disks defined in the `config/filesystems.php`.
    |
    */
    
    'storage_disk' => env('FILAMENT_STORAGE_DISK', 'public'),
    'storage_path' => env('FILAMENT_STORAGE_PATH', 'uploads'),
    
    
];