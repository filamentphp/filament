<?php

return [

    'label' => 'Profil',

    'form' => [

        'email' => [
            'label' => 'E-Mail-Adresse',
        ],

        'name' => [
            'label' => 'Name',
        ],

        'password' => [
            'label' => 'Neues Passwort',
            'validation_attribute' => 'Passwort',
        ],

        'password_confirmation' => [
            'label' => 'Passwort bestätigen',
            'validation_attribute' => 'Passwortbestätigung',
        ],
        'current_password' => [
            'label' => 'Aktuelles Passwort',
            'below_content' => 'Aus Sicherheitsgründen bestätigen Sie bitte Ihr Passwort, um fortzufahren.',
            'validation_attribute' => 'aktuelles Passwort',
        ],

        'actions' => [

            'save' => [
                'label' => 'Änderung speichern',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Zwei-Faktor-Authentifizierung (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Anfrage zur Änderung der E-Mail-Adresse gesendet',
            'body' => 'Eine Anfrage zur Änderung Ihrer E-Mail-Adresse wurde an :email gesendet. Bitte überprüfen Sie Ihre E-Mails, um die Änderung zu bestätigen.',
        ],

        'saved' => [
            'title' => 'Gespeichert',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Abbrechen',
        ],

    ],

];
