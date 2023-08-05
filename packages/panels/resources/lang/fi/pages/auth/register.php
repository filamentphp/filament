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

    'messages' => [
        'throttled' => 'Liian monta tilin luomisen yritystä. Yritä uudelleen :seconds sekunnin päästä.',
    ],

];
