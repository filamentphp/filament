<?php

return [

    'title' => 'Erősítsd meg az email címed',

    'heading' => 'Erősítsd meg az email címed',

    'actions' => [

        'resend_notification' => [
            'label' => 'Újra küldés',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Nem kaptad meg az emailt?',
        'notification_sent' => 'Küldtünk egy emailt a(z) :email címre, amely tartalmazza az email címed ellenőrzésére vonatkozó utasításokat.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Újra elküldtük az emailt.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Túl sok újraküldési kísérlet',
            'body' => 'Kérjük, hogy próbáld meg újra :second másodperc múlva.',
        ],

    ],

];
