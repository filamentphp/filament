<?php

return [

    'title' => '验证邮箱地址',

    'heading' => '验证邮箱地址',

    'actions' => [

        'resend_notification' => [
            'label' => '已重新发送',
        ],

    ],

    'messages' => [
        'notification_not_received' => '没有收到我们的邮件？',
        'notification_sent' => '我们已经向 :email 发送了一封验证邮件。',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => '我们已经重新发送了邮件。',
        ],

        'notification_resend_throttled' => [
            'title' => '发送邮件次数过多',
            'body' => '请在 :seconds 秒后重试。',
        ],

    ],

];
