<?php

return [

    'direction' => 'ltr',

    'skip_to_content' => [
        'label' => 'Vai al contenuto',
    ],

    'actions' => [

        'billing' => [
            'label' => 'Gestisci abbonamento',
        ],

        'logout' => [
            'label' => 'Disconnetti',
        ],

        'open_database_notifications' => [
            'label' => 'Apri notifiche',
            'label_with_unread_count' => '{1} Notifiche, :count notifica non letta|[2,*] Notifiche, :count notifiche non lette',
        ],

        'open_user_menu' => [
            'label' => 'Menu utente',
        ],

        'sidebar' => [

            'collapse' => [
                'label' => 'Comprimi sidebar',
            ],

            'expand' => [
                'label' => 'Espandi sidebar',
            ],

        ],

        'theme_switcher' => [

            'label' => 'Tema',

            'dark' => [
                'label' => 'Attiva tema scuro',
            ],

            'light' => [
                'label' => 'Attiva tema chiaro',
            ],

            'system' => [
                'label' => 'Attiva tema di sistema',
            ],

        ],

    ],
    'navigation' => [
        'label' => 'Navigazione laterale',
    ],

    'topbar' => [
        'label' => 'Barra superiore',
    ],

    'avatar' => [
        'alt' => 'Avatar di :name',
    ],

    'logo' => [
        'alt' => 'Logo di :name',
    ],

    'tenant_menu' => [

        'search_field' => [
            'label' => 'Cerca Tenant',
            'placeholder' => 'Cerca',
        ],

    ],

];
