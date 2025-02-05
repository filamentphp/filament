<?php

return [

    'title' => 'Lähtesta oma salasõna',

    'heading' => 'Lähtesta oma salasõna',

    'form' => [

        'email' => [
            'label' => 'E-posti aadress',
        ],

        'password' => [
            'label' => 'Salasõna',
            'validation_attribute' => 'salasõna',
        ],

        'password_confirmation' => [
            'label' => 'Kinnita salasõna',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Lähtesta salasõna',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Liiga palju lähtestamise katseid',
            'body' => 'Palun proovi uuesti :seconds sekundi pärast.',
        ],

    ],

];
