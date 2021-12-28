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
            'name' => 'super_admin'
        ],
        'filament_user' => [
            'enabled' => true,
            'name' => 'filament_user'
        ],
    ],

    'default_permission_prefixes' => [
        'view_any_',
        'view_',
        'create_',
        'update_',
        'delete_',
        'delete_any_',
    ]

];
