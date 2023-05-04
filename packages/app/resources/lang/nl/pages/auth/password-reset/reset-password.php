<?php

return [

    'title' => 'Wachtwoord opnieuw instellen',

    'heading' => 'Wachtwoord opnieuw instellen',

    'buttons' => [

        'reset' => [
            'label' => 'Wachtwoord opnieuw instellen',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'E-mailadres',
        ],

        'password' => [
            'label' => 'Wachtwoord',
            'validation_attribute' => 'wachtwoord',
        ],

        'passwordConfirmation' => [
            'label' => 'Wachtwoord bevestigen',
        ],

    ],

    'messages' => [
        'throttled' => 'Te veel pogingen. Probeer het opnieuw over :seconds seconden.',
    ],

];
