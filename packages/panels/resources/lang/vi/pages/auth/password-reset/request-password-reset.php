<?php

return [

    'title' => 'Đặt lại mật khẩu',

    'heading' => 'Quên mật khẩu?',

    'actions' => [

        'login' => [
            'label' => 'quay lại đăng nhập',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Địa chỉ email',
        ],

        'actions' => [

            'request' => [
                'label' => 'Gửi email',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'body' => 'Nếu tài khoản của bạn không tồn tại, bạn sẽ không nhận được email.',
        ],

        'throttled' => [
            'title' => 'Quá nhiều yêu cầu',
            'body' => 'Vui lòng thử lại sau :seconds giây.',
        ],

    ],

];
