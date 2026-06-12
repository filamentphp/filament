<?php

return [

    'title' => 'Autentificare',

    'heading' => 'Loghează-te în contul tau',

    'actions' => [

        'register' => [
            'before' => 'sau',
            'label' => 'creează cont',
        ],

        'request_password_reset' => [
            'label' => 'Ai uitat parola?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email',
        ],

        'password' => [
            'label' => 'Parola',
        ],

        'remember' => [
            'label' => 'Ține-mă minte',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Autentificare',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Verificați-vă identitatea',

        'subheading' => 'Pentru a continua autentificarea, trebuie să vă verificați identitatea.',

        'form' => [

            'provider' => [
                'label' => 'Cum doriți să verificați?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Confirmă autentificarea',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Emailul sau parola nu sunt corecte.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Prea multe încercări de autentificare',
            'body' => 'Vă rugăm să încercați din nou în :seconds secunde.',
        ],

    ],

];
