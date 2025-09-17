<?php

return [

    'title' => 'Wachtwoord opnieuw instellen',

    'heading' => 'Wachtwoord vergeten?',

    'actions' => [

        'login' => [
            'label' => 'terug naar inloggen',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-mailadres',
        ],

        'actions' => [

            'request' => [
                'label' => 'E-mail verzenden',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'body' => 'Als uw account niet bestaat, ontvangt u de e-mail niet.',
        ],

        'throttled' => [
            'title' => 'Te veel pogingen',
            'body' => 'Probeer het opnieuw over :seconds seconden.',
        ],

    ],

];
