<?php

return [

    'title' => '驗證你的電郵地址',

    'heading' => '驗證你的電郵地址',

    'actions' => [

        'resend_notification' => [
            'label' => '重新傳送',
        ],

    ],

    'messages' => [
        'notification_not_received' => '未收到驗證電郵？',
        'notification_sent' => '驗證電郵已傳送至 :email。請按電郵內的指示驗證你的電郵地址。',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => '驗證電郵已重新傳送。',
        ],

        'notification_resend_throttled' => [
            'title' => '重發次數過多',
            'body' => '請在 :seconds 秒後再試。',
        ],

    ],

];
