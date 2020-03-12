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

    /*
    |--------------------------------------------------------------------------
    | Laravel Form builder config overrides.
    |-------------------------------------------------------------------------- 
    |
    | These config options are merged into the Laravel Form builder.
    |
    */

    'laravel-form-builder' => [

        'defaults' => [
            'wrapper_class'       => 'mb-6',
            'wrapper_error_class' => '',
            'label_class'         => 'block text-sm font-medium leading-5 text-gray-700 mb-1',
            'field_class'         => 'appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5',
            'field_error_class'   => 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:shadow-outline-red',
            'help_block_class'    => 'mt-2 text-sm font-medium text-gray-500',
            'error_class'         => 'mt-2 text-sm text-red-600',
            // 'required_class'      => ''
        ],

    ],

];