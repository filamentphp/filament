<?php

return [

    'title' => 'Resetiraj svoju lozinku',

    'heading' => 'Resetiraj svoju lozinku',

    'form' => [

        'email' => [
            'label' => 'Adresa e-pošte',
        ],

        'password' => [
            'label' => 'Lozinka',
            'validation_attribute' => 'lozinka',
        ],

        'password_confirmation' => [
            'label' => 'Potvrdi lozinku',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Resetiraj lozinku',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Previše pokušaja resetiranja.',
            'body' => 'Molim te, pokušaj ponovno za :seconds sekundi.',
        ],

    ],

];
