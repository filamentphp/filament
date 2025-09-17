<?php

return [

    'title' => 'Верификација адресе ваше е-поште',

    'heading' => 'Верификација адресе ваше е-поште',

    'actions' => [

        'resend_notification' => [
            'label' => 'Поново пошаљи',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Још увек нисте примили е-пошту?',
        'notification_sent' => 'Послали смо поруку на :email са инструкцијама за верификацију адресе ваше е-поште.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Поново смо послали поруку.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Превише поновних слања',
            'body' => 'Покушајте поново за :seconds s.',
        ],

    ],

];
