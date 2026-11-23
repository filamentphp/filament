<?php

return [

    'direction' => 'ltr',

    'skip_to_content' => [
        'label' => '본문으로 건너뛰기',
    ],

    'actions' => [

        'billing' => [
            'label' => '구독 관리',
        ],

        'logout' => [
            'label' => '로그아웃',
        ],

        'open_database_notifications' => [
            'label' => '알림 열기',
            'label_with_unread_count' => '{1} 알림, 읽지 않은 알림 :count개|[2,*] 알림, 읽지 않은 알림 :count개',
        ],

        'open_user_menu' => [
            'label' => '사용자 메뉴',
        ],

        'sidebar' => [

            'collapse' => [
                'label' => '사이드바 접기',
            ],

            'expand' => [
                'label' => '사이드바 펼치기',
            ],

        ],

        'theme_switcher' => [

            'label' => '테마',

            'dark' => [
                'label' => '다크 모드',
            ],

            'light' => [
                'label' => '라이트 모드',
            ],

            'system' => [
                'label' => '시스템 테마 사용',
            ],

        ],

    ],
    'navigation' => [
        'label' => '사이드바 내비게이션',
    ],

    'topbar' => [
        'label' => '상단 바',
    ],

    'avatar' => [
        'alt' => ':name 아바타',
    ],

    'logo' => [
        'alt' => ':name 로고',
    ],

    'tenant_menu' => [

        'search_field' => [
            'label' => '테넌트 검색',
            'placeholder' => '검색',
        ],

    ],

];
