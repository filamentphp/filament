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
        'notification_sent' => 'Küldtünk egy e-mailt a :email címre, amely tartalmazza az e-mail címed ellenőrzésére vonatkozó utasításokat.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Újra elküldtük az e-mailt.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Túl sok újraküldési kísérlet',
            'body' => 'Kérjük, próbálja meg újra :second másodperc múlva.',
        ],

    ],

];
