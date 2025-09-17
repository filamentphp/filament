<?php

return [

    'title' => 'Registracija',

    'heading' => 'Registrujte se',

    'actions' => [

        'login' => [
            'before' => 'ili',
            'label' => 'Prijavite se',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Adresa e-pošte',
        ],

        'name' => [
            'label' => 'Ime',
        ],

        'password' => [
            'label' => 'Lozinka',
            'validation_attribute' => 'lozinka',
        ],

        'password_confirmation' => [
            'label' => 'Potvrdite lozinku',
        ],

        'actions' => [

            'register' => [
                'label' => 'Registrujte se',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Previše pokušaja registracije',
            'body' => 'Molim vas, pokušajte ponovo za :seconds sekundi.',
        ],

    ],

];
