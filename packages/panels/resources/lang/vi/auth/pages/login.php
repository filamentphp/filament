<?php

return [

    'title' => 'Đăng nhập',

    'heading' => 'Đăng nhập',

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
            'label' => 'Địa chỉ email',
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

    'multi_factor' => [

        'heading' => 'Xác minh danh tính',

        'subheading' => 'Để tiếp tục đăng nhập, bạn cần xác minh danh tính.',

        'form' => [

            'provider' => [
                'label' => 'Bạn muốn xác minh như thế nào?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Xác nhận đăng nhập',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Thông tin đăng nhập không chính xác.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Quá nhiều lần đăng nhập thất bại',
            'body' => 'Vui lòng thử lại sau :seconds giây.',
        ],

    ],

];
