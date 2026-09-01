<?php

return [

    'title' => 'Logg inn',

    'heading' => 'Logg inn på konto',

    'actions' => [

        'register' => [
            'before' => 'eller',
            'label' => 'opprett ny konto',
        ],

        'request_password_reset' => [
            'label' => 'Glemt passordet?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-postadresse',
        ],

        'password' => [
            'label' => 'Passord',
        ],

        'remember' => [
            'label' => 'Husk meg',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Logg inn',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Bekreft identiteten din',

        'subheading' => 'For å fortsette innloggingen må du bekrefte identiteten din.',

        'form' => [

            'provider' => [
                'label' => 'Hvordan vil du bekrefte?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Bekreft innlogging',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Påloggingsinformasjonen stemmer ikke med våre data',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'For mange forsøk på innlogging.',
            'body' => 'Vennligst prøv igjen om :seconds sekunder.',
        ],

    ],

];
