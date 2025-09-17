<?php

return [

    'title' => 'Inloggen',

    'heading' => 'Inloggen op je account',

    'actions' => [

        'register' => [
            'before' => 'of',
            'label' => 'maak een account aan',
        ],

        'request_password_reset' => [
            'label' => 'Wachtwoord vergeten?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-mailadres',
        ],

        'password' => [
            'label' => 'Wachtwoord',
        ],

        'remember' => [
            'label' => 'Onthoud mij',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Inloggen',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Verifieer je identiteit',

        'subheading' => 'Om inloggen te voltooien, moet je je identiteit verifiëren.',

        'form' => [

            'provider' => [
                'label' => 'Hoe wil je verifiëren?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Verifiëren',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Onjuiste inloggegevens.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Te veel inlogpogingen',
            'body' => 'Probeer het opnieuw over :seconds seconden.',
        ],

    ],

];
