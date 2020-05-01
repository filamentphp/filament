<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    |
    | The initial system permissions needed by Filament. These are added when
    | running the initial seed `php artisan db:seed --class=FilamentSeeder`
    |
    */

    [
        'name' => 'view users',
        'description' => 'Allow a user to view users.',
        'guard_name' => 'web',
        'is_system' => 1,
    ],

    [
        'name' => 'edit users',
        'description' => 'Allow a user to edit users.',
        'guard_name' => 'web',
        'is_system' => 1,
    ],

    [
        'name' => 'create users',
        'description' => 'Allow a user to create users.',
        'guard_name' => 'web',
        'is_system' => 1,
    ],

    [
        'name' => 'delete users',
        'description' => 'Allow a user to delete users.',
        'guard_name' => 'web',
        'is_system' => 1,
    ],

    [
        'name' => 'edit user roles',
        'description' => 'Allow a user to edit user roles.',
        'guard_name' => 'web',
        'is_system' => 1,
    ],

    [
        'name' => 'edit user permissions',
        'description' => 'Allow a user to edit user permissions.',
        'guard_name' => 'web',
        'is_system' => 1,
    ],

    [
        'name' => 'view roles',
        'description' => 'Allow a user to view roles.',
        'guard_name' => 'web',
        'is_system' => 1,
    ],

    [
        'name' => 'edit roles',
        'description' => 'Allow a user to edit roles.',
        'guard_name' => 'web',
        'is_system' => 1,
    ],

    [
        'name' => 'create roles',
        'description' => 'Allow a user to create roles.',
        'guard_name' => 'web',
        'is_system' => 1,
    ],

    [
        'name' => 'delete roles',
        'description' => 'Allow a user to delete roles.',
        'guard_name' => 'web',
        'is_system' => 1,
    ],

    [
        'name' => 'view permissions',
        'description' => 'Allow a user to view permissions.',
        'guard_name' => 'web',
        'is_system' => 1,
    ],

    [
        'name' => 'edit permissions',
        'description' => 'Allow a user to edit permissions.',
        'guard_name' => 'web',
        'is_system' => 1,
    ],

    [
        'name' => 'create permissions',
        'description' => 'Allow a user to create permissions.',
        'guard_name' => 'web',
        'is_system' => 1,
    ],

    [
        'name' => 'delete permissions',
        'description' => 'Allow a user to delete permissions.',
        'guard_name' => 'web',
        'is_system' => 1,
    ],

];