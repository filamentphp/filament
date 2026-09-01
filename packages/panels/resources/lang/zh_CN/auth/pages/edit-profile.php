<?php

return [

    'label' => '个人资料',

    'form' => [

        'email' => [
            'label' => '邮箱地址',
        ],

        'name' => [
            'label' => '姓名',
        ],

        'password' => [
            'label' => '新密码',
        ],

        'password_confirmation' => [
            'label' => '确认新密码',
        ],

        'current_password' => [
            'label' => '当前密码',
            'below_content' => '出于安全原因，请确认您的密码以继续。',
            'validation_attribute' => '当前密码',
        ],

        'actions' => [

            'save' => [
                'label' => '保存',
            ],

        ],

    ],

    'notifications' => [

        'saved' => [
            'title' => '已保存',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => '取消',
        ],

    ],

];
