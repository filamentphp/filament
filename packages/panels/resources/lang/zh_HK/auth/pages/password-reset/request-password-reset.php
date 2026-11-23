<?php

return [

    'title' => '重設你的密碼',

    'heading' => '忘記密碼？',

    'actions' => [

        'login' => [
            'label' => '返回登入',
        ],

    ],

    'form' => [

        'email' => [
            'label' => '電郵地址',
        ],

        'actions' => [

            'request' => [
                'label' => '發送電郵',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'body' => '如果你的帳戶不存在，你將不會收到電郵。',
        ],

        'throttled' => [
            'title' => '要求次數過多',
            'body' => '請在 :seconds 秒後再試。',
        ],

    ],

];
