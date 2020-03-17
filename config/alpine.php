<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Name
    |--------------------------------------------------------------------------
    |
    | This is the name used by Alpine.
    |
    */

    'name' => env('ALPINE_NAME', 'Alpine'),

    /*
    |--------------------------------------------------------------------------
    | Base Route Prefix
    |--------------------------------------------------------------------------
    |
    | This is the URI path where Alpine will be accessible from.
    |
    */

    'path' => env('ALPINE_PATH_NAME', 'cp'),

    /*
    |--------------------------------------------------------------------------
    | Redirects
    |--------------------------------------------------------------------------
    |
    | This is the URI path where Alpine will be accessible from.
    |
    */

    'redirects' => [

        'admin' => 'alpine.admin.dashboard', // string or named route to redirect users when successfully authenticated.

    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    |
    | Navigations used by Alpine.
    |
    */

    'nav' => [

        'admin' => [

            'dashboard' => [
                'label' => 'alpine::admin.dashboard',
                'icon' => 'heroicons/outline-md/md-home', // path to SVG icon using Alpine::svg() helper
                'url' => 'alpine.admin.dashboard', // string or named route
                'active' => 'alpine.admin.dashboard', // string or named route (may also be an array @link https://github.com/dwightwatson/active)
            ],

            /* TODO

            'Resources' => [
                'label' => 'alpine::admin.resources',
                'icon' => 'heroicons/outline-md/md-collection',
                'url' => '#',
            ],

            'globals' => [
                'label' => 'alpine::admin.globals',
                'icon' => 'heroicons/outline-md/md-globe',
                'url' => '#',
            ],

            */

            'users' => [
                'label' => 'alpine::admin.users',
                'icon' => 'heroicons/outline-md/md-user-group',
                'url' => '#',
                'ability' => 'view users',
            ],

            /* TODO 

            'Permissions' => [
                'label' => 'alpine::admin.permissions',
                'icon' => 'heroicons/outline-md/md-lock-closed',
                'url' => '#',
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
    | The Eloquent Models used by Alpine.
    |
    */
    
    'models' => [
        // 'resource' => Alpine\Models\Resource::class,
    ],    

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will be attached to every route type in Alpine.
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
    | Storage
    |--------------------------------------------------------------------------
    |
    | This is the storage disk Alpine will use to put file uploads, you may
    | use any of the disks defined in the `config/filesystems.php`.
    |
    */

    'storage_disk' => env('ALPINE_STORAGE_DISK', 'local'),
    'storage_path' => env('ALPINE_STORAGE_PATH', 'public/alpine'),
    
];