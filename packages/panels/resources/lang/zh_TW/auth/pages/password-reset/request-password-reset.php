<?php

return [

    'title' => '重設您的密碼',

    'heading' => '忘記密碼？',

    'actions' => [

        'login' => [
            'label' => '返回登入',
        ],

    ],

    'form' => [

        'email' => [
            'label' => '電子郵件地址',
        ],

        'actions' => [

            'request' => [
                'label' => '發送電子郵件',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'body' => '如果您的帳戶不存在，您將不會收到電子郵件。',
        ],

        'throttled' => [
            'title' => '請求次數過多',
            'body' => '請在 :seconds 秒後再試。',
        ],

    ],

];
