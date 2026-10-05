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
            'validation_attribute' => '密码',
        ],

        'password_confirmation' => [
            'label' => '确认新密码',
            'validation_attribute' => '确认密码',
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

    'multi_factor_authentication' => [
        'label' => '双重验证（2FA）',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => '邮箱变更申请已发送',
            'body' => '验证邮件已发送至 :email，请查收并确认更换邮箱。',
        ],

        'saved' => [
            'title' => '已保存',
        ],

        'throttled' => [
            'title' => '请求过于频繁，请在 :seconds 秒后再试。',
            'body' => '请在 :seconds 秒后再试。',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => '取消',
        ],

    ],

];
