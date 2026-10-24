<?php

return [

    'direction' => 'ltr',

    'skip_to_content' => [
        'label' => '跳至主要內容',
    ],

    'actions' => [

        'billing' => [
            'label' => '管理訂閱',
        ],

        'logout' => [
            'label' => '登出',
        ],

        'open_database_notifications' => [
            'label' => '通知',
            'label_with_unread_count' => '{1} 通知，:count 則未讀|[2,*] 通知，:count 則未讀',
        ],

        'open_user_menu' => [
            'label' => '用戶菜單',
        ],

        'sidebar' => [

            'collapse' => [
                'label' => '收起側邊欄',
            ],

            'expand' => [
                'label' => '展開側邊欄',
            ],

        ],

        'theme_switcher' => [

            'label' => '主題',

            'dark' => [
                'label' => '切換至暗色模式',
            ],

            'light' => [
                'label' => '切換至亮色模式',
            ],

            'system' => [
                'label' => '切換至系統模式',
            ],

        ],

    ],

    'navigation' => [
        'label' => '側邊欄導覽',
    ],

    'topbar' => [
        'label' => '頂端列',
    ],

    'avatar' => [
        'alt' => ':name 的頭像',
    ],

    'logo' => [
        'alt' => ':name 標誌',
    ],

    'tenant_menu' => [

        'search_field' => [
            'label' => '租戶搜尋',
            'placeholder' => '搜尋',
        ],

    ],

];
