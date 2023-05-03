<?php

return [

    'title' => 'Anmelden',

    'heading' => 'Melden Sie sich an.',

    'buttons' => [

        'authenticate' => [
            'label' => 'Anmelden',
        ],

        'register' => [
            'before' => 'oder',
            'label' => 'erstellen Sie ein Konto',
        ],

        'request_password_reset' => [
            'label' => 'Passwort vergessen?',
        ],
    ],

    'fields' => [

        'email' => [
            'label' => 'E-Mail-Adresse',
        ],

        'password' => [
            'label' => 'Passwort',
        ],

        'remember' => [
            'label' => 'Angemeldet bleiben',
        ],

    ],

    'messages' => [
        'failed' => 'Diese Kombination aus Zugangsdaten wurde nicht in unserer Datenbank gefunden.',
        'throttled' => 'Zu viele Loginversuche. Versuchen Sie es bitte in :seconds Sekunden nochmal.',
    ],

];
