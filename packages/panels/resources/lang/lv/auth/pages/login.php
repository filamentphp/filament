<?php

return [

    'title' => 'Pieteikšanās',

    'heading' => 'Pierakstīties savā kontā',

    'actions' => [

        'register' => [
            'before' => 'vai',
            'label' => 'reģistrēties',
        ],

        'request_password_reset' => [
            'label' => 'Aizmirsāt paroli?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-pasta adrese',
        ],

        'password' => [
            'label' => 'Parole',
        ],

        'remember' => [
            'label' => 'Atcerēties mani',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Pierakstīties',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Šie akreditācijas dati neatbilst mūsu ierakstiem.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Pārāk daudz pieteikšanās mēģinājumu.',
            'body' => 'Lūdzu, mēģiniet vēlreiz pēc :seconds sekundēm.',
        ],

    ],

];
