<?php

return [

    'title' => 'Accesso',

    'heading' => 'Accedi',

    'actions' => [

        'register' => [
            'before' => 'o',
            'label' => 'crea un account',
        ],

        'request_password_reset' => [
            'label' => 'Hai smarrito la password?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email',
        ],

        'password' => [
            'label' => 'Password',
        ],

        'remember' => [
            'label' => 'Ricordami',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Accedi',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Verifica la tua identità',

        'subheading' => 'Per accedere è necessario verificare la tua identità.',

        'form' => [

            'provider' => [
                'label' => 'Come desideri verificare?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Conferma l\'accesso',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'I dati di accesso non sono corretti.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Troppi tentativi di accesso',
            'body' => 'Riprova tra :seconds secondi.',
        ],

    ],

];
