<?php

return [

    'title' => 'Prijava',

    'heading' => 'Prijavite se',

    'actions' => [

        'register' => [
            'before' => 'ili',
            'label' => 'se registrirajte za korisnički nalog',
        ],

        'request_password_reset' => [
            'label' => 'Zaboravljena lozinka?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Adresa e-pošte',
        ],

        'password' => [
            'label' => 'Lozinka',
        ],

        'remember' => [
            'label' => 'Zapamti me',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Prijavite se',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Pogrešno korisničko ime ili lozinka',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Previše pokušaja prijave',
            'body' => 'Molim vas, pokušajte ponovo za :seconds sekundi.',
        ],

    ],

];
