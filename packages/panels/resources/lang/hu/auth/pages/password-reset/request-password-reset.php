<?php

return [

    'title' => 'Jelszó visszaállítása',

    'heading' => 'Elfelejtetted a jelszavad?',

    'actions' => [

        'login' => [
            'label' => 'vissza a bejelentkezéshez',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email cím',
        ],

        'actions' => [

            'request' => [
                'label' => 'Email küldése',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'body' => 'Ha nem tartozik fiók ehhez az e-mail címhez, akkor nem fogunk üzenetet küldeni.',
        ],

        'throttled' => [
            'title' => 'Túl sok próbálkozás',
            'body' => 'Kérjük, próbáld meg újra :seconds másodperc múlva.',
        ],

    ],

];
