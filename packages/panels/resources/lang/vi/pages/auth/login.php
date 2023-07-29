<?php

return [

    'title' => 'Đăng nhập',

    'heading' => 'Đăng nhập vào tài khoản của bạn',

    'actions' => [

        'register' => [
            'before' => 'hoặc',
            'label' => 'đăng ký tài khoản',
        ],

        'request_password_reset' => [
            'label' => 'Quên mật khẩu?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-mail',
        ],

        'password' => [
            'label' => 'Mật khẩu',
        ],

        'remember' => [
            'label' => 'Ghi nhớ đăng nhập',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Đăng nhập',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'E-mail hoặc mật khẩu không hợp lệ.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Đăng nhập sai quá nhiều lần. Vui lòng thử lại sau :seconds giây nữa.',
        ],

    ],

];
