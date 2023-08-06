<?php

return [

    'title' => 'Xác thực địa chỉ email',

    'heading' => 'Xác thực địa chỉ email',

    'actions' => [

        'resend_notification' => [
            'label' => 'Gửi lại',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Chưa nhận được email?',
        'notification_sent' => 'Chúng tôi đã gửi một email đến :email chứa hướng dẫn về cách xác thực địa chỉ email của bạn.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Đã gửi lại email',
        ],

        'notification_resend_throttled' => [
            'title' => 'Quá nhiều lần gửi lại',
            'body' => 'Vui lòng thử lại sau :seconds giây.',
        ],

    ],

];
