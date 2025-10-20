<?php

return [

    'title' => 'Resetuj svoju lozinku',

    'heading' => 'Resetuj svoju lozinku',

    'form' => [

        'email' => [
            'label' => 'Adresa e-pošte',
        ],

        'password' => [
            'label' => 'Lozinka',
            'validation_attribute' => 'lozinka',
        ],

        'password_confirmation' => [
            'label' => 'Potvrdite lozinku',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Resetuj lozinku',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Predao se previše pokušaja resetovanja.',
            'body' => 'Molim vas, pokušajte ponovo za :seconds sekundi.',
        ],

    ],

];
