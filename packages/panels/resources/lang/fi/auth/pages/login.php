<?php

return [

    'title' => 'Kirjaudu',

    'heading' => 'Kirjaudu tilillesi',

    'actions' => [

        'register' => [
            'before' => 'tai',
            'label' => 'luo tili',
        ],

        'request_password_reset' => [
            'label' => 'Salasana hukassa?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Sähköpostiosoite',
        ],

        'password' => [
            'label' => 'Salasana',
        ],

        'remember' => [
            'label' => 'Muista minut',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Kirjaudu',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Vahvista identiteetti',

        'subheading' => 'Kirjautumista varten sinun tulee vahvistaa identiteettisi.',

        'form' => [

            'provider' => [
                'label' => 'Miten haluaisit vahvistaa?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Vahvista kirjautuminen',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Kirjautuminen epäonnistui.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Liian monta kirjautumisyritystä',
            'body' => 'Yritä uudelleen :seconds sekunnin kuluttua.',
        ],

    ],

];
