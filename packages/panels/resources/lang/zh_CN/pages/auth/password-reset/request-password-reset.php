<?php

return [

    'title' => '重置密码',

    'heading' => '忘记密码？',

    'actions' => [

        'login' => [
            'label' => '返回登录页面',
        ],

    ],

    'form' => [

        'email' => [
            'label' => '邮箱地址',
        ],

        'actions' => [

            'request' => [
                'label' => '发送邮件',
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
