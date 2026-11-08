<?php

return [

    'direction' => 'ltr',

    'skip_to_content' => [
        'label' => 'Langsung ke konten',
    ],

    'actions' => [

        'billing' => [
            'label' => 'Kelola langganan',
        ],

        'logout' => [
            'label' => 'Keluar',
        ],

        'open_database_notifications' => [
            'label' => 'Buka notifikasi',
            'label_with_unread_count' => '{1} Notifikasi, :count belum dibaca|[2,*] Notifikasi, :count belum dibaca',
        ],

        'open_user_menu' => [
            'label' => 'Menu pengguna',
        ],

        'sidebar' => [

            'collapse' => [
                'label' => 'Persempit menu',
            ],

            'expand' => [
                'label' => 'Perluas menu',
            ],

        ],

        'theme_switcher' => [

            'label' => 'Tema',

            'dark' => [
                'label' => 'Mode Gelap',
            ],

            'light' => [
                'label' => 'Mode Terang',
            ],

            'system' => [
                'label' => 'Sesuai tema perangkat',
            ],

        ],

    ],
    'navigation' => [
        'label' => 'Navigasi sidebar',
    ],

    'topbar' => [
        'label' => 'Bilah atas',
    ],

    'avatar' => [
        'alt' => 'Avatar :name',
    ],

    'logo' => [
        'alt' => 'Logo :name',
    ],

    'tenant_menu' => [

        'search_field' => [
            'label' => 'Pencarian tenant',
            'placeholder' => 'Cari',
        ],

    ],

];
