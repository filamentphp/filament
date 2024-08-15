<?php

return [

    'title' => 'Resetujte svoju lozinku',

    'heading' => 'Resetujte svoju lozinku',

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
                'label' => 'Resetuj lozinku',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Previše pokušaja resetovanja.',
            'body' => 'Molim pokušajte ponovno za :seconds sekundi.',
        ],

    ],

];
