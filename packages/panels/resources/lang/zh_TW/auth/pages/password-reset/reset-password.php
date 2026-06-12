<?php

return [

    'title' => '重設您的密碼',

    'heading' => '重設您的密碼',

    'form' => [

        'email' => [
            'label' => '電子郵件地址',
        ],

        'password' => [
            'label' => '密碼',
            'validation_attribute' => '密碼',
        ],

        'password_confirmation' => [
            'label' => '確認密碼',
        ],

        'actions' => [

            'reset' => [
                'label' => '重設密碼',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => '重設嘗試次數過多',
            'body' => '請在 :seconds 秒後再試。',
        ],

    ],

];
