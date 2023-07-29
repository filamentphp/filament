<?php

return [

    'title' => 'Bejelentkezés',

    'heading' => 'Bejelentkezés a fiókba',

    'form' => [

        'email' => [
            'label' => 'E-mail cím',
        ],

        'password' => [
            'label' => 'Jelszó',
        ],

        'remember' => [
            'label' => 'Emlékezz rám',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Bejelentkezés',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Hibás e-mail cím vagy jelszó.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Kérjük várjon :seconds másodpercet a következő próbálkozás előtt.',
        ],

    ],

];
