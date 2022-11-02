<?php

return [

    'title' => 'Đăng nhập',

    'heading' => 'Đăng nhập vào tài khoản của bạn',

    'buttons' => [

        'submit' => [
            'label' => 'Đăng nhập',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'E-mail',
        ],

        'password' => [
            'label' => 'Mật khẩu',
        ],

        'remember' => [
            'label' => 'Ghi nhớ đăng nhập',
        ],

    ],

    'messages' => [
        'failed' => 'E-mail hoặc mật khẩu không hợp lệ.',
        'throttled' => 'Đăng nhập sai quá nhiều lần. Vui lòng thử lại sau :seconds giây nữa.',
    ],

];
