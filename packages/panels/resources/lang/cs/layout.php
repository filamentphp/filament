<?php

return [

    'direction' => 'ltr',

    'skip_to_content' => [
        'label' => 'Přeskočit na obsah',
    ],

    'actions' => [

        'billing' => [
            'label' => 'Správa předplatného',
        ],

        'logout' => [
            'label' => 'Odhlásit se',
        ],

        'open_database_notifications' => [
            'label' => 'Zobrazit notifikace',
            'label_with_unread_count' => '{1} Oznámení, :count nepřečtené oznámení|[2,4] Oznámení, :count nepřečtená oznámení|[0,*] Oznámení, :count nepřečtených oznámení',
        ],

        'open_user_menu' => [
            'label' => 'Nabídka uživatele',
        ],

        'sidebar' => [

            'collapse' => [
                'label' => 'Skrýt boční panel',
            ],

            'expand' => [
                'label' => 'Otevřít boční panel',
            ],

        ],

        'theme_switcher' => [

            'label' => 'Téma',

            'dark' => [
                'label' => 'Zapnout tmavý režim',
            ],

            'light' => [
                'label' => 'Zapnout světlý režim',
            ],

            'system' => [
                'label' => 'Použít nastavení systému',
            ],

        ],

    ],

    'navigation' => [
        'label' => 'Boční navigace',
    ],

    'topbar' => [
        'label' => 'Horní panel',
    ],

    'avatar' => [
        'alt' => 'Profilový obrázek pro :name',
    ],

    'logo' => [
        'alt' => 'Logo :name',
    ],

    'tenant_menu' => [

        'search_field' => [
            'label' => 'Vyhledávání nájemce',
            'placeholder' => 'Hledat',
        ],

    ],

];
