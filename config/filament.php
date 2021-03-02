<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Filament Path
    |--------------------------------------------------------------------------
    |
    | The default is `admin` but you can change it to whatever works best and
    | doesn't conflict with the routing in your application.
    |
    | You may also change the domain where Filament should be active. If the
    | domain is empty, all domains will be valid.
    |
    */

    'path' => 'admin',
    
    'domain' => '',

    /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    |
    | This is the namespace and directory that Filament will automatically
    | register pages from.
    |
    */

    'pages' => [
        'namespace' => 'App\\Filament\\Pages',
        'path' => app_path('Filament/Pages'),
    ],

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
    | Widgets
    |--------------------------------------------------------------------------
    |
    | This is the namespace and directory that Filament will automatically
    | register widgets from.
    |
    */

    'widgets' => [
        'namespace' => 'App\\Filament\\Widgets',
        'path' => app_path('Filament/Widgets'),
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
