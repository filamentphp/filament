<?php

return [

    'title' => 'Prijava',

    'heading' => 'Prijava',

    'actions' => [

        'register' => [
            'before' => 'ili',
            'label' => 'se registruj',
        ],

        'request_password_reset' => [
            'label' => 'Zaboravili ste lozinku?',
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

    'multi_factor' => [

        'heading' => 'Potvrdi svoj identitet',

        'subheading' => 'Kako bi nastavili sa prijavom potrebno je da potvrdite svoj identitet.',

        'form' => [

            'provider' => [
                'label' => 'Kako želite da potvrdite svoj identitet?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Potvrdi prijavu',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Dati akreditivi ne odgovaraju našim zapisima.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Previše pokušaja prijave',
            'body' => 'Pokušajte ponovo za :seconds s.',
        ],

    ],

];
