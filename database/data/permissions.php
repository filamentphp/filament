<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    |
    | The initial system permissions needed by Filament. These are create using
    | the `php artisan filament:install` command.
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
        'name' => 'edit user roles',
        'description' => 'Allow a user to edit user roles.',
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
        'name' => 'create roles',
        'description' => 'Allow a user to create roles.',
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
        'name' => 'create permissions',
        'description' => 'Allow a user to create permissions.',
        'guard_name' => 'web',
        'is_system' => 1,
    ],

];