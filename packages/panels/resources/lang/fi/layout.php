<?php

return [

    'direction' => 'ltr',

    'skip_to_content' => [
        'label' => 'Siirry sisältöön',
    ],

    'actions' => [

        'billing' => [
            'label' => 'Hallitse tilausta',
        ],

        'logout' => [
            'label' => 'Kirjaudu ulos',
        ],

        'open_database_notifications' => [
            'label' => 'Avaa ilmoitukset',
            'label_with_unread_count' => '{1} Ilmoitukset, :count ilmoitus lukematta|[2,*] Ilmoitukset, :count ilmoitusta lukematta',
        ],

        'open_user_menu' => [
            'label' => 'Käyttäjävalikko',
        ],

        'sidebar' => [

            'collapse' => [
                'label' => 'Sulje sivupalkki',
            ],

            'expand' => [
                'label' => 'Laajenna sivupalkki',
            ],

        ],

        'theme_switcher' => [

            'label' => 'Teema',

            'dark' => [
                'label' => 'Tumma tila',
            ],

            'light' => [
                'label' => 'Vaalea tila',
            ],

            'system' => [
                'label' => 'Järjestelmän tila',
            ],

        ],

    ],

    'navigation' => [
        'label' => 'Sivupalkin navigaatio',
    ],

    'topbar' => [
        'label' => 'Yläpalkki',
    ],

    'avatar' => [
        'alt' => ':name avatar',
    ],

    'logo' => [
        'alt' => ':name logo',
    ],

    'tenant_menu' => [

        'search_field' => [
            'label' => 'Asiakkaiden haku',
            'placeholder' => 'Hae',
        ],

    ],

];
