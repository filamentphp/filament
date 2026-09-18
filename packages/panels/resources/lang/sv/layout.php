<?php

return [

    'direction' => 'ltr',

    'skip_to_content' => [
        'label' => 'Hoppa till innehåll',
    ],

    'actions' => [

        'billing' => [
            'label' => 'Hantera prenumeration',
        ],

        'logout' => [
            'label' => 'Logga ut',
        ],

        'open_database_notifications' => [
            'label' => 'Öppna notiser',
            'label_with_unread_count' => '{1} Notiser, :count oläst notis|[2,*] Notiser, :count olästa notiser',
        ],

        'open_user_menu' => [
            'label' => 'Användarmeny',
        ],

        'sidebar' => [

            'collapse' => [
                'label' => 'Dölj sidopanel',
            ],

            'expand' => [
                'label' => 'Visa sidopanel',
            ],

        ],

        'theme_switcher' => [

            'label' => 'Tema',

            'dark' => [
                'label' => 'Använd mörkt tema',
            ],

            'light' => [
                'label' => 'Använd ljust tema',
            ],

            'system' => [
                'label' => 'Följ systemets tema',
            ],

        ],

    ],

    'navigation' => [
        'label' => 'Navigering i sidopanel',
    ],

    'topbar' => [
        'label' => 'Toppfält',
    ],

    'avatar' => [
        'alt' => 'Avatar för :name',
    ],

    'logo' => [
        'alt' => ':name logotyp',
    ],

    'tenant_menu' => [

        'search_field' => [
            'label' => 'Sök bland klienter',
            'placeholder' => 'Sök',
        ],

    ],

];
