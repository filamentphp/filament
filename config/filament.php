<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Filament Name
    |--------------------------------------------------------------------------
    |
    | The default name for Filament.
    |
    */

    'name' => 'Filament',

    /*
    |--------------------------------------------------------------------------
    | Filament Admin Path
    |--------------------------------------------------------------------------
    |
    | The default is `admin` but you can change it to whatever works best and
    | doesn't conflict with the routing in your application.
    |
    */

    'admin_path' => 'admin',

    /*
    |--------------------------------------------------------------------------
    | Resources
    |--------------------------------------------------------------------------
    |
    | This is the namespace and directory that Filament will automatically
    | register resources from.
    |
    */

    'resources' => [
        'namespace' => 'App\\Filament\\Resources',
        'path' => app_path('Filament/Resources'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    |
    | This is the namespace and directory that Filament will automatically
    | register roles from.
    |
    */

    'roles' => [
        'namespace' => 'App\\Filament\\Roles',
        'path' => app_path('Filament/Roles'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | This is the storage disk Filament will use to put media. You may use any
    | of the disks defined in the `config/filesystems.php`.
    |
    */
    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DRIVER', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | This is the cache disk Filament will use, you may use any of the disks
    | defined in the `config/filesystems.php`.
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
