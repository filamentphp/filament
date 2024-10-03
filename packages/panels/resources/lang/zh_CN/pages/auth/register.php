<?php

return [

    'title' => '注册',

    'heading' => '注册',

    'actions' => [

        'login' => [
            'before' => '或者',
            'label' => '登录你的账号',
        ],

    ],

    'form' => [

        'email' => [
            'label' => '邮箱地址',
        ],

        'name' => [
            'label' => '姓名',
        ],

        'password' => [
            'label' => '密码',
            'validation_attribute' => '密码',
        ],

        'password_confirmation' => [
            'label' => '确认密码',
        ],

        'actions' => [

            'register' => [
                'label' => '提交注册',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => '尝试注册次数过多',
            'body' => '请在 :seconds 秒后重试。',
        ],

    ],

];
