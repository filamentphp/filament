<?php

return [

    'title' => 'Đăng ký',

    'heading' => 'Đăng ký tài khoản',

    'actions' => [

        'login' => [
            'before' => 'hoặc',
            'label' => 'đăng nhập tài khoản',
        ],
    ],

    'form' => [

        'email' => [
            'label' => 'Địa chỉ email',
        ],

        'name' => [
            'label' => 'Họ tên',
        ],

        'password' => [
            'label' => 'Mật khẩu',
            'validation_attribute' => 'mật khẩu',
        ],

        'password_confirmation' => [
            'label' => 'Xác nhận mật khẩu',
        ],

        'actions' => [

            'register' => [
                'label' => 'Đăng ký',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Quá nhiều lần thử đăng ký.',
            'body' => 'Vui lòng thử lại sau :seconds giây.',
        ],

    ],

];
