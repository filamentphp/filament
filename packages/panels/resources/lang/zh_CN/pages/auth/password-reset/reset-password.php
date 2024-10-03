<?php

return [

    'title' => '重置密码',

    'heading' => '重置密码',

    'form' => [

        'email' => [
            'label' => '邮箱地址',
        ],

        'password' => [
            'label' => '密码',
            'validation_attribute' => '密码',
        ],

        'password_confirmation' => [
            'label' => '确认密码',
        ],

        'actions' => [

            'reset' => [
                'label' => '重置密码',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => '尝试次数过多',
            'body' => '请在 :seconds 秒后重试。',
        ],

    ],

];
