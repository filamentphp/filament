<?php

return [

    'title' => 'Paroles maiņa',

    'heading' => 'Mainīt paroli',

    'form' => [

        'email' => [
            'label' => 'E-pasta adrese',
        ],

        'password' => [
            'label' => 'Parole',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Paroles apstiprinājums',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Mainīt paroli',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Pārāk daudz mēģinājumu',
            'body' => 'Lūdzu, mēģiniet vēlreiz pēc :seconds sekundēm.',
        ],

    ],

];
