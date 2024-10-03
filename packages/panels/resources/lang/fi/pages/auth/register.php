<?php

return [

    'title' => 'Rekisteröi',

    'heading' => 'Luo tili',

    'actions' => [

        'login' => [
            'before' => 'tai',
            'label' => 'kirjaudu tilillesi',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Sähköpostiosoite',
        ],

        'name' => [
            'label' => 'Nimi',
        ],

        'password' => [
            'label' => 'Salasana',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Vahvista salasana',
        ],

        'actions' => [

            'register' => [
                'label' => 'Luo tili',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Liian monta tilin luomisen yritystä',
            'body' => 'Yritä uudelleen :seconds sekunnin päästä.',
        ],
    ],

];
