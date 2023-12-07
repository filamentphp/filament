<?php

return [

    'title' => 'Resetirajte svoju lozinku',

    'heading' => 'Resetirajte svoju lozinku',

    'form' => [

        'email' => [
            'label' => 'Email adresa',
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
            'body' => 'Molim pokušajte ponovno za :seconds sekundi.',
        ],

    ],

];
