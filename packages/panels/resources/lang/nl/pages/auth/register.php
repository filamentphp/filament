<?php

return [

    'title' => 'Registreren',

    'heading' => 'Registreren',

    'actions' => [

        'login' => [
            'before' => 'of',
            'label' => 'inloggen op je account',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-mailadres',
        ],

        'name' => [
            'label' => 'Naam',
        ],

        'password' => [
            'label' => 'Wachtwoord',
            'validation_attribute' => 'wachtwoord',
        ],

        'password_confirmation' => [
            'label' => 'Wachtwoord bevestigen',
        ],

        'actions' => [

            'register' => [
                'label' => 'Registreren',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Te veel registratiepogingen',
            'body' => 'Probeer het opnieuw over :seconds seconden.',
        ],

    ],

];
