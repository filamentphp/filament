<?php

return [

    'title' => 'Registreer',

    'heading' => 'Teken in',

    'actions' => [

        'login' => [
            'before' => 'of',
            'label' => 'teken aan by jou rekening',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-pos adres',
        ],

        'name' => [
            'label' => 'Naam',
        ],

        'password' => [
            'label' => 'Wagwoord',
            'validation_attribute' => 'wagwoord',
        ],

        'password_confirmation' => [
            'label' => 'Bevestig wagwoord',
        ],

        'actions' => [

            'register' => [
                'label' => 'Teken in',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Te veel registrasiepogings',
            'body' => 'Probeer asseblief weer oor :seconds sekondes.',
        ],

    ],

];
