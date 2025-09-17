<?php

return [

    'label' => 'Profil',

    'form' => [

        'email' => [
            'label' => 'Email cím',
        ],

        'name' => [
            'label' => 'Név',
        ],

        'password' => [
            'label' => 'Új jelszó',
            'validation_attribute' => 'jelszó',
        ],

        'password_confirmation' => [
            'label' => 'Új jelszó megerősítése',
            'validation_attribute' => 'jelszó megerősítés',
        ],

        'current_password' => [
            'label' => 'Jelenlegi jelszó',
            'below_content' => 'A biztonság érdekében kérjük, erősítsd meg a jelszavadat a folytatáshoz.',
            'validation_attribute' => 'jelenlegi jelszó',
        ],

        'actions' => [

            'save' => [
                'label' => 'Mentés',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Kétfaktoros hitelesítés (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Email cím változtatási kérelem elküldve',
            'body' => 'Az email címed megváltoztatására vonatkozó kérelem el lett küldve a(z) :email címre. Kérjük, ellenőrizd az emailjeidet a változtatás igazolásához.',
        ],

        'saved' => [
            'title' => 'Mentve',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Mégsem',
        ],

    ],

];
