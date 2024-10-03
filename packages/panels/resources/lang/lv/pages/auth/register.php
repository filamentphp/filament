<?php

return [

    'title' => 'Reģistrācija',

    'heading' => 'Reģistrēties',

    'actions' => [

        'login' => [
            'before' => 'vai',
            'label' => 'pierakstīties savā kontā',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-pasta adrese',
        ],

        'name' => [
            'label' => 'Vārds',
        ],

        'password' => [
            'label' => 'Parole',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Paroles apstiprinājums',
        ],

        'actions' => [

            'register' => [
                'label' => 'Reģistrēties',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Pārāk daudz reģistrēšanās mēģinājumu',
            'body' => 'Lūdzu, mēģiniet vēlreiz pēc :seconds sekundēm.',
        ],

    ],

];
