<?php

return [

    'title' => 'Passwort zurücksetzen',

    'heading' => 'Passwort zurücksetzen',

    'form' => [

        'email' => [
            'label' => 'E-Mail-Adresse',
        ],

        'password' => [
            'label' => 'Passwort',
            'validation_attribute' => 'Passwort',
        ],

        'password_confirmation' => [
            'label' => 'Passwort bestätigen',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Passwort zurücksetzen',
            ],

        ],

    ],

    'messages' => [
        'throttled' => 'Zu viele Versuche. Versuchen Sie es bitte in :seconds Sekunden nochmal.',
    ],

];
