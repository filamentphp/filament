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