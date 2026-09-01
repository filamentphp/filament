<?php

return [

    'title' => 'Passwort zurücksetzen',

    'heading' => 'Passwort vergessen?',

    'actions' => [

        'login' => [
            'label' => 'zurück zum Login',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-Mail-Adresse',
        ],

        'actions' => [

            'request' => [
                'label' => 'E-Mail zusenden',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'body' => 'Wenn Ihr Konto nicht existiert, erhalten Sie keine E-Mail.',
        ],

        'throttled' => [
            'title' => 'Zu viele Versuche.',
            'body' => 'Versuchen Sie es bitte in :seconds Sekunden nochmal.',
        ],

    ],

];
