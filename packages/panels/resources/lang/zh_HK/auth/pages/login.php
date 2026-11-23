<?php

return [

    'title' => '登入',

    'heading' => '登入帳號',

    'actions' => [

        'register' => [
            'before' => '或',
            'label' => '註冊新帳戶',
        ],

        'request_password_reset' => [
            'label' => '忘記密碼？',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-Mail 位址',
        ],

        'password' => [
            'label' => '密碼',
        ],

        'remember' => [
            'label' => '記住我',
        ],

        'actions' => [

            'authenticate' => [
                'label' => '登入',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => '核實你的身分',

        'subheading' => '請先核實身分，再繼續登入。',

        'form' => [

            'provider' => [
                'label' => '你想用哪種方式核實身分？',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => '確認登入',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => '所提供的帳號密碼與資料庫中的記錄不相符。',

    ],

    'notifications' => [

        'throttled' => [
            'title' => '嘗試登入次數過多',
            'body' => '請在 :seconds 秒後再試一次。',
        ],

    ],

];
