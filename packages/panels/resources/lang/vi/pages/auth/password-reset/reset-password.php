<?php

return [

    'title' => 'Đặt lại mật khẩu',

    'heading' => 'Đặt lại mật khẩu',

    'form' => [

        'email' => [
            'label' => 'Địa chỉ email',
        ],

        'password' => [
            'label' => 'Mật khẩu',
            'validation_attribute' => 'mật khẩu',
        ],

        'password_confirmation' => [
            'label' => 'Xác nhận mật khẩu',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Đặt lại mật khẩu',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Quá nhiều yêu cầu đặt lại mật khẩu',
            'body' => 'Vui lòng thử lại sau :seconds giây.',
        ],

    ],

];
