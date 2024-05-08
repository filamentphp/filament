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
            'label' => 'E-postedresse',
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
