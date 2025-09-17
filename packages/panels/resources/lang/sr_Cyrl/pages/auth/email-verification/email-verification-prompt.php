<?php

return [

    'title' => 'Потврдите своју адресу е-поште',

    'heading' => 'Потврдите своју адресу е-поште',

    'actions' => [

        'resend_notification' => [
            'label' => 'Поново пошаљи',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Нисте примили е-пошту коју смо послали?',
        'notification_sent' => 'Послали смо е-пошту на :email са упутством о томе како потврдити своју адресу е-поште.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Поново смо послали е-пошту.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Превише покушаја поновног слања',
            'body' => 'Молим вас, покушајте поновно за :seconds секунди.',
        ],

    ],

];
