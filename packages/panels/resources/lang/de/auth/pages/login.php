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

    'multi_factor' => [

        'heading' => 'Verifizieren Sie Ihre Identität',

        'subheading' => 'Um mit der Anmeldung fortzufahren, müssen Sie Ihre Identität verifizieren.',

        'form' => [

            'provider' => [
                'label' => 'Wie möchten Sie sich verifizieren?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Anmeldung bestätigen',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Diese Kombination aus Zugangsdaten wurde nicht in unserer Datenbank gefunden.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Zu viele Loginversuche.',
            'body' => ' Bitte in :seconds Sekunden nochmal versuchen.',
        ],

    ],

];
