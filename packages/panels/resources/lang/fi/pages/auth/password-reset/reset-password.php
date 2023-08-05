<?php

return [

    'title' => 'Palauta salasana',

    'heading' => 'Palauta salasana',

    'form' => [

        'email' => [
            'label' => 'Sähköpostiosoite',
        ],

        'password' => [
            'label' => 'Salasana',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Vahvista salasana',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Palauta salasana',
            ],

        ],

    ],

    'messages' => [
        'throttled' => 'Liian monta palautukse yritystä. Yritä uudelleen :seconds sekunnin kuluttua.',
    ],

];
