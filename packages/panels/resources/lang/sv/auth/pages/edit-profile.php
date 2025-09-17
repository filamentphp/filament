<?php

return [

    'label' => 'Profil',

    'form' => [

        'email' => [
            'label' => 'Mejladress',
        ],

        'name' => [
            'label' => 'Namn',
        ],

        'password' => [
            'label' => 'Nytt lösenord',
            'validation_attribute' => 'lösenord',
        ],

        'password_confirmation' => [
            'label' => 'Bekräfta nytt lösenord',
            'validation_attribute' => 'lösenordsbekräftelse',
        ],

        'current_password' => [
            'label' => 'Nuvarande lösenord',
            'below_content' => 'För säkerhet, vänligen bekräfta ditt lösenord för att fortsätta.',
            'validation_attribute' => 'nuvarande lösenord',
        ],

        'actions' => [

            'save' => [
                'label' => 'Spara ändringar',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Tvåfaktorsautentisering (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Begäran om ändring av mejladress har skickats',
            'body' => 'En begäran om att ändra din mejladress har skickats till :email. Kontrollera din mejl för att verifiera ändringen.',
        ],

        'saved' => [
            'title' => 'Sparades',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Avbryt',
        ],

    ],

];
