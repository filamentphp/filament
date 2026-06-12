<?php

return [

    'label' => 'Profil',

    'form' => [

        'email' => [
            'label' => 'E-postadresse',
        ],

        'name' => [
            'label' => 'Navn',
        ],

        'password' => [
            'label' => 'Nytt passord',
            'validation_attribute' => 'passord',
        ],

        'password_confirmation' => [
            'label' => 'Bekreft nytt passord',
            'validation_attribute' => 'bekreftelse av passord',
        ],

        'current_password' => [
            'label' => 'Nåværende passord',
            'below_content' => 'Av sikkerhetsgrunner, bekreft passordet ditt for å fortsette.',
            'validation_attribute' => 'nåværende passord',
        ],

        'actions' => [

            'save' => [
                'label' => 'Lagre endringer',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Tofaktorautentisering (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Forespørsel om endring av e-postadresse sendt',
            'body' => 'En forespørsel om å endre e-postadressen din er sendt til :email. Sjekk e-posten din for å bekrefte endringen.',
        ],

        'saved' => [
            'title' => 'Lagret',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Tilbake',
        ],

    ],

];
