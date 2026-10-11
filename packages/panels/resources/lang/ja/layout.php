<?php

return [

    'direction' => 'ltr',

    'skip_to_content' => [
        'label' => '本文へスキップ',
    ],

    'actions' => [

        'billing' => [
            'label' => 'サブスクリプションを管理',
        ],

        'logout' => [
            'label' => 'ログアウト',
        ],

        'open_database_notifications' => [
            'label' => 'お知らせを確認',
            'label_with_unread_count' => '{1} お知らせ、未読:count件|[2,*] お知らせ、未読:count件',
        ],

        'open_user_menu' => [
            'label' => 'ユーザーメニュー',
        ],

        'sidebar' => [

            'collapse' => [
                'label' => 'サイドバーを折り畳む',
            ],

            'expand' => [
                'label' => 'サイドバーを展開する',
            ],

        ],

        'theme_switcher' => [

            'label' => 'テーマ',

            'dark' => [
                'label' => 'ダークモードに切り替える',
            ],

            'light' => [
                'label' => 'ライトモードに切り替える',
            ],

            'system' => [
                'label' => 'システムテーマを有効にする',
            ],

        ],

    ],

    'navigation' => [
        'label' => 'サイドバーナビゲーション',
    ],

    'topbar' => [
        'label' => 'トップバー',
    ],

    'avatar' => [
        'alt' => ':nameのアバター',
    ],

    'logo' => [
        'alt' => ':nameロゴ',
    ],

    'tenant_menu' => [

        'search_field' => [
            'label' => 'テナントを検索',
            'placeholder' => '検索',
        ],

    ],

];
