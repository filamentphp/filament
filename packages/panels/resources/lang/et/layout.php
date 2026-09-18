<?php

return [

    'direction' => 'ltr',

    'skip_to_content' => [
        'label' => 'Otse sisu juurde',
    ],

    'actions' => [

        'billing' => [
            'label' => 'Halda tellimust',
        ],

        'logout' => [
            'label' => 'Logi välja',
        ],

        'open_database_notifications' => [
            'label' => 'Ava teated',
            'label_with_unread_count' => '{1} Teateid, :count lugemata notification|[2,*] Teateid, :count lugemata teateid',
        ],

        'open_user_menu' => [
            'label' => 'Kasutaja menüü',
        ],

        'sidebar' => [

            'collapse' => [
                'label' => 'Sulge külgriba',
            ],

            'expand' => [
                'label' => 'Ava külgriba',
            ],

        ],

        'theme_switcher' => [

            'label' => 'Teema',

            'dark' => [
                'label' => 'Tume teema',
            ],

            'light' => [
                'label' => 'Hele teema',
            ],

            'system' => [
                'label' => 'Süsteemi teema',
            ],

        ],

    ],
    'navigation' => [
        'label' => 'Küljepaneeli navigatsioon',
    ],

    'topbar' => [
        'label' => 'Ülemine paneel',
    ],

    'avatar' => [
        'alt' => 'Kasutaja :name avatar',
    ],

    'logo' => [
        'alt' => ':name logo',
    ],

    'tenant_menu' => [

        'search_field' => [
            'label' => 'Asuri otsing',
            'placeholder' => 'Otsi',
        ],

    ],

];
