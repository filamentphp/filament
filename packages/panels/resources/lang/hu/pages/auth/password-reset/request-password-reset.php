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

        'throttled' => [
            'title' => 'Túl sok próbálkozás',
            'body' => 'Kérjük, próbáld meg újra :second másodperc múlva.',
        ],

    ],

];
