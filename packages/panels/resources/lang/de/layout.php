<?php

return [

    'direction' => 'ltr',

    'skip_to_content' => [
        'label' => 'Zum Inhalt springen',
    ],

    'actions' => [

        'billing' => [
            'label' => 'Abonnement verwalten',
        ],

        'logout' => [
            'label' => 'Abmelden',
        ],

        'open_database_notifications' => [
            'label' => 'Benachrichtigungen öffnen',
            'label_with_unread_count' => '{1} Benachrichtigungen, :count ungelesene Benachrichtigung|[2,*] Benachrichtigungen, :count ungelesene Benachrichtigungen',
        ],

        'open_user_menu' => [
            'label' => 'Benutzermenü',
        ],

        'sidebar' => [

            'collapse' => [
                'label' => 'Seitenleiste einklappen',
            ],

            'expand' => [
                'label' => 'Seitenleiste ausklappen',
            ],

        ],

        'theme_switcher' => [

            'label' => 'Thema',

            'dark' => [
                'label' => 'Dark Mode einschalten',
            ],

            'light' => [
                'label' => 'Light Mode einschalten',
            ],

            'system' => [
                'label' => 'Systemthema benutzen',
            ],

        ],

    ],

    'navigation' => [
        'label' => 'Seitennavigation',
    ],

    'topbar' => [
        'label' => 'Menüleiste',
    ],

    'avatar' => [
        'alt' => 'Avatar von :name',
    ],

    'logo' => [
        'alt' => 'Logo von :name',
    ],

    'tenant_menu' => [

        'search_field' => [
            'label' => 'Mandant suchen',
            'placeholder' => 'Suchen',
        ],

    ],

];
