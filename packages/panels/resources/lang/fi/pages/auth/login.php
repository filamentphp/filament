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

    'notifications' => [
        'failed' => 'Kirjautuminen epäonnistui.',
        'throttled' => 'Liian monta kirjautumisyritystä. Yritä uudelleen :seconds sekunnin kuluttua.',
    ],

];
