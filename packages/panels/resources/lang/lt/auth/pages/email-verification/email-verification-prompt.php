<?php

return [

    'title' => 'Patvirtinkite el. pašto adresą',

    'heading' => 'Patvirtinkite el. pašto adresą',

    'actions' => [

        'resend_notification' => [
            'label' => 'Išsiųsti dar kartą',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Negavote el. laiško?',
        'notification_sent' => 'Nusiuntėme instrukcijas į el. paštą :email kaip patvirtinti el. pašto adresą.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Persiuntėme el. laišką dar karta.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Per daug bandymų išsiųsti dar karta',
            'body' => 'Bandykite dar kartą už :seconds sekundžių.',
        ],

    ],

];
