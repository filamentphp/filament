<?php

return [

    'direction' => 'ltr',

    'skip_to_content' => [
        'label' => 'Skip to content',
    ],

    'actions' => [

        'billing' => [
            'label' => 'Manage subscription',
        ],

        'logout' => [
            'label' => 'Sign out',
        ],

        'open_database_notifications' => [
            'label' => 'Notifications',
            'label_with_unread_count' => '{1} Notifications, :count unread notification|[2,*] Notifications, :count unread notifications',
        ],

        'open_user_menu' => [
            'label' => 'User menu',
        ],

        'sidebar' => [

            'collapse' => [
                'label' => 'Collapse sidebar',
            ],

            'expand' => [
                'label' => 'Expand sidebar',
            ],

        ],

        'theme_switcher' => [

            'label' => 'Theme',

            'dark' => [
                'label' => 'Enable dark theme',
            ],

            'light' => [
                'label' => 'Enable light theme',
            ],

            'system' => [
                'label' => 'Enable system theme',
            ],

        ],

    ],

    'navigation' => [
        'label' => 'Sidebar navigation',
    ],

    'topbar' => [
        'label' => 'Topbar',
    ],

    'avatar' => [
        'alt' => 'Avatar of :name',
    ],

    'logo' => [
        'alt' => ':name logo',
    ],

    'tenant_menu' => [

        'search_field' => [
            'label' => 'Tenant search',
            'placeholder' => 'Search',
        ],

    ],

];
