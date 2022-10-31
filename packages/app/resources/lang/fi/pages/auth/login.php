<?php

return [

    'title' => 'Kirjaudu',

    'heading' => 'Kirjaudu tilillesi',

    'buttons' => [

        'authenticate' => [
            'label' => 'Kirjaudu',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'Sähköpostiosoite',
        ],

        'password' => [
            'label' => 'Salasana',
        ],

        'remember' => [
            'label' => 'Muista minut',
        ],

    ],

    'messages' => [
        'failed' => 'Kirjautuminen epäonnistui.',
        'throttled' => 'Liian monta kirjautumisyritystä. Yritä uudelleen :seconds sekunnin kuluttua.',
    ],

];
