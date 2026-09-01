<?php

return [

    'title' => 'Bekræft din e-mail',

    'heading' => 'Bekræft din e-mail',

    'actions' => [

        'resend_notification' => [
            'label' => 'Gensend',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Har ikke modtaget den e-mail, vi sendte?',
        'notification_sent' => 'Vi har sendt en e-mail til :email med instruktioner om, hvordan du verificerer din e-mail.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Vi har sendt e-mailen igen.',
        ],

        'notification_resend_throttled' => [
            'title' => 'For mange forsøg på at sende igen',
            'body' => 'Prøv igen om :seconds sekunder.',
        ],

    ],

];
