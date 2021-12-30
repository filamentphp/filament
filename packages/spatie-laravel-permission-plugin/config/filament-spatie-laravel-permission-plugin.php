<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Locales
    |--------------------------------------------------------------------------
    |
    | These are the locales that Filament will use to put translate resource
    | content. They may be overridden for each resource by setting the
    | `$translatableLocales` property.
    |
    */

    'default_roles' => [
        'super_admin' => [
            'enabled' => true,
            'role_name' => 'super_admin'
        ],
        'filament_user' => [
            'enabled' => true,
            'role_name' => 'filament_user'
        ],
    ],

    'default_permission_prefixes' => [
        'view',
        'view_any',
        'create',
        'delete',
        'delete_any',
        'update',
    ]

];
