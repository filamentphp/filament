<?php

return [

    'title' => 'ยืนยันที่อยู่อีเมล',

    'heading' => 'ยืนยันที่อยู่อีเมล',

    'actions' => [

        'resend_notification' => [
            'label' => 'ส่งใหม่',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'ยังไม่ได้รับอีเมลที่เราส่งไหม?',
        'notification_sent' => 'เราได้ส่งอีเมลไปยัง :email พร้อมคำแนะนำในการยืนยันที่อยู่อีเมลของคุณไปแล้ว',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'เราได้ส่งอีเมลอีกครั้งให้แล้ว',
        ],

        'notification_resend_throttled' => [
            'title' => 'จำนวนครั้งในการส่งอีเมลได้ถึงขีดจำกัดแล้ว',
            'body' => 'กรุณาลองส่งใหม่อีก :seconds วินาที',
        ],

    ],

];
