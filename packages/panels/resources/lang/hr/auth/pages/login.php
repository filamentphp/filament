<?php

return [

    'title' => 'Prijava',

    'heading' => 'Prijavi se',

    'actions' => [

        'register' => [
            'before' => 'ili',
            'label' => 'se registriraj za korisnički račun',
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
                'label' => 'Prijavi se',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Pogrešno korisničko ime ili lozinka',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Previše pokušaja prijave',
            'body' => 'Molim te, pokušaj ponovno za :seconds sekundi.',
        ],

    ],

];
