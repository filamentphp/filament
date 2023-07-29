<?php

return [

    'title' => 'Kirjaudu',

    'heading' => 'Kirjaudu tilillesi',

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
            'title' => 'Liian monta kirjautumisyritystä. Yritä uudelleen :seconds sekunnin kuluttua.',
        ],

    ],

];
