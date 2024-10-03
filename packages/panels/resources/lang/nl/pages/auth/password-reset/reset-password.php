<?php

return [

    'title' => 'Wachtwoord opnieuw instellen',

    'heading' => 'Wachtwoord opnieuw instellen',

    'form' => [

        'email' => [
            'label' => 'E-mailadres',
        ],

        'password' => [
            'label' => 'Wachtwoord',
            'validation_attribute' => 'wachtwoord',
        ],

        'password_confirmation' => [
            'label' => 'Wachtwoord bevestigen',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Wachtwoord opnieuw instellen',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Te veel resetpogingen',
            'body' => 'Probeer het opnieuw over :seconds seconden.',
        ],

    ],

];
