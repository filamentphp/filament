<?php

return [

    'title' => 'Inloggen',

    'heading' => 'Inloggen op je account',

    'buttons' => [

        'authenticate' => [
            'label' => 'Inloggen',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'E-mailadres',
        ],

        'password' => [
            'label' => 'Wachtwoord',
        ],

        'remember' => [
            'label' => 'Herinner mij',
        ],

    ],

    'messages' => [
        'failed' => 'Onjuiste inloggegevens.',
        'throttled' => 'Te veel inlogpogingen. Probeer opnieuw over :seconds seconden.',
    ],

];
