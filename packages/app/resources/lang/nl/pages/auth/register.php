<?php

return [

    'title' => 'Registreren',

    'heading' => 'Registreren',

    'buttons' => [

        'login' => [
            'before' => 'of',
            'label' => 'inloggen op je account',
        ],

        'register' => [
            'label' => 'Registreren',
        ],

    ],

    'fields' => [

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

    ],

    'messages' => [
        'throttled' => 'Te veel pogingen. Probeer het opnieuw over :seconds seconden.',
    ],

];
