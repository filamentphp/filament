<?php

return [

    'title' => 'Имэйл баталгаажуулах',

    'heading' => 'Имэйл хаягаа баталгаажуулах',

    'actions' => [

        'resend_notification' => [
            'label' => 'Дахин илгээх',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Бидний илгээсэн имэйлийг хүлээж аваагүй юу?',
        'notification_sent' => 'Бид :email хаяг руу имэйл хаягаа баталгаажуулах мэдээллийг илгээсэн.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Имэйл илгээгдсэн.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Олон тооны илгээх оролдлого',
            'body' => ':seconds секундын дараа дахин оролдоно уу!',
        ],

    ],

];
