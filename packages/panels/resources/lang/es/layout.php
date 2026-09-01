<?php

return [

    'direction' => 'ltr',

    'skip_to_content' => [
        'label' => 'Saltar al contenido',
    ],

    'actions' => [

        'billing' => [
            'label' => 'Administrar suscripción',
        ],

        'logout' => [
            'label' => 'Salir',
        ],

        'open_database_notifications' => [
            'label' => 'Abrir notificaciones',
            'label_with_unread_count' => '{1} Notificaciones, :count notificación no leída|[2,*] Notificaciones, :count notificaciones no leídas',
        ],

        'open_user_menu' => [
            'label' => 'Menú del usuario',
        ],

        'sidebar' => [

            'collapse' => [
                'label' => 'Contraer barra lateral',
            ],

            'expand' => [
                'label' => 'Expandir barra lateral',
            ],

        ],

        'theme_switcher' => [

            'label' => 'Tema',

            'dark' => [
                'label' => 'A modo oscuro',
            ],

            'light' => [
                'label' => 'A modo claro',
            ],

            'system' => [
                'label' => 'A modo del sistema',
            ],

        ],

    ],

    'navigation' => [
        'label' => 'Barra de navegación lateral',
    ],

    'topbar' => [
        'label' => 'Barra superior',
    ],

    'avatar' => [
        'alt' => 'Avatar de :name',
    ],

    'logo' => [
        'alt' => ':name logo',
    ],

    'tenant_menu' => [

        'search_field' => [
            'label' => 'Buscar inquilino',
            'placeholder' => 'Buscar',
        ],

    ],

];
