<?php

return [
    'direction' => 'ltr',
    'skip_to_content' => [
        'label' => 'Pumunta sa content',
    ],
    'actions' => [
        'billing' => [
            'label' => 'I-manage ang subscription',
        ],
        'logout' => [
            'label' => 'Mag-sign out',
        ],
        'open_database_notifications' => [
            'label' => 'Mga notification',
            'label_with_unread_count' => '{1} Mga notification, :count hindi pa nababasang notification|[2,*] Mga notification, :count hindi pa nababasang notification',
        ],
        'open_user_menu' => [
            'label' => 'Menu ng user',
        ],
        'sidebar' => [
            'collapse' => [
                'label' => 'I-collapse ang sidebar',
            ],
            'expand' => [
                'label' => 'I-expand ang sidebar',
            ],
        ],
        'theme_switcher' => [
            'label' => 'Display theme',
            'dark' => [
                'label' => 'I-enable ang dark theme',
            ],
            'light' => [
                'label' => 'I-enable ang light theme',
            ],
            'system' => [
                'label' => 'I-enable ang system theme',
            ],
        ],
    ],
    'navigation' => [
        'label' => 'Navigation sa sidebar',
    ],
    'topbar' => [
        'label' => 'Bar sa itaas',
    ],
    'avatar' => [
        'alt' => 'Avatar ni :name',
    ],
    'logo' => [
        'alt' => 'Logo ng :name',
    ],
    'tenant_menu' => [
        'search_field' => [
            'label' => 'Maghanap ng tenant',
            'placeholder' => 'Maghanap',
        ],
    ],
];
