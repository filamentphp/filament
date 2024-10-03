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

    'notifications' => [

        'throttled' => [
            'title' => 'Zu viele Versuche.',
            'body' => 'Bitte in :seconds Sekunden nochmal versuchen.',

        ],

    ],

];
