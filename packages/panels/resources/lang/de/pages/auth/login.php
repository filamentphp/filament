<?php

return [

    'title' => 'Anmelden',

    'heading' => 'Melden Sie sich an.',

    'actions' => [

        'register' => [
            'before' => 'oder',
            'label' => 'erstellen Sie ein Konto',
        ],

        'request_password_reset' => [
            'label' => 'Passwort vergessen?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-Mail-Adresse',
        ],

        'password' => [
            'label' => 'Passwort',
        ],

        'remember' => [
            'label' => 'Angemeldet bleiben',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Anmelden',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Diese Kombination aus Zugangsdaten wurde nicht in unserer Datenbank gefunden.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Zu viele Loginversuche. Versuchen Sie es bitte in :seconds Sekunden nochmal.',
        ],

    ],

];
