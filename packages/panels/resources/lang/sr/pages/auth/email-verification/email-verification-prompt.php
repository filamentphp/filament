<?php

return [

    'title' => 'Потврдите своју имејл адресу',

    'heading' => 'Потврдите своју имејл адресу',

    'actions' => [

        'resend_notification' => [
            'label' => 'Поново пошаљи',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Нисте примили имејл који смо послали?',
        'notification_sent' => 'Послали смо имејл на :email са упутствима о томе како да потврдите своју имејл адресу.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Поново смо послали имејл.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Превише покушаја поновног слања',
            'body' => 'Молимо вас, покушајте поново за :seconds секунди.',
        ],

    ],

];
