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
        'guard_name' => 'web',
        'is_system' => 1,
    ],

    [
        'name' => 'edit users',
        'guard_name' => 'web',
        'is_system' => 1,
    ],

    [
        'name' => 'create users',
        'guard_name' => 'web',
        'is_system' => 1,
    ],

    [
        'name' => 'edit user roles',
        'guard_name' => 'web',
        'is_system' => 1,
    ],

];