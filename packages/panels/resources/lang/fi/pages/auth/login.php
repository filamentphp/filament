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
