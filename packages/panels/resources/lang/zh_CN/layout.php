<?php

return [

    'direction' => 'ltr',

    'skip_to_content' => [
        'label' => '跳至主要内容',
    ],

    'actions' => [

        'billing' => [
            'label' => '管理订阅',
        ],

        'logout' => [
            'label' => '退出登录',
        ],

        'open_database_notifications' => [
            'label' => '打开通知',
            'label_with_unread_count' => '通知，:count 条未读',
        ],

        'open_user_menu' => [
            'label' => '用户菜单',
        ],

        'sidebar' => [

            'collapse' => [
                'label' => '折叠侧边栏',
            ],

            'expand' => [
                'label' => '展开侧边栏',
            ],

        ],

        'theme_switcher' => [

            'label' => '主题',

            'dark' => [
                'label' => '切换至深色主题',
            ],

            'light' => [
                'label' => '切换至浅色主题',
            ],

            'system' => [
                'label' => '按照系统主题切换',
            ],

        ],

    ],
    'navigation' => [
        'label' => '侧边栏导航',
    ],

    'topbar' => [
        'label' => '顶部栏',
    ],

    'avatar' => [
        'alt' => ':name 的头像',
    ],

    'logo' => [
        'alt' => ':name 标志',
    ],

    'tenant_menu' => [

        'search_field' => [
            'label' => '租户搜索',
            'placeholder' => '搜索',
        ],

    ],

];
