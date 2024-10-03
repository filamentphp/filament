<?php

return [

    'title' => 'Prijava',

    'heading' => 'Prijavite se',

    'actions' => [

        'register' => [
            'before' => 'ili',
            'label' => 'se registrirajte za korisnički račun',
        ],

        'request_password_reset' => [
            'label' => 'Zaboravili ste lozinku?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email adresa',
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
            'body' => 'Molim pokušajte ponovno za :seconds sekundi.',
        ],

    ],

];
