<?php

return [

    'label' => 'Profila',

    'form' => [

        'email' => [
            'label' => 'Helbide elektronikoa',
        ],

        'name' => [
            'label' => 'Izena',
        ],

        'password' => [
            'label' => 'Pasahitz berria',
            'validation_attribute' => 'pasahitza',
        ],

        'password_confirmation' => [
            'label' => 'Berretsi pasahitz berria',
            'validation_attribute' => 'pasahitzaren berrespena',
        ],

        'current_password' => [
            'label' => 'Uneko pasahitza',
            'below_content' => 'Segurtasunerako, berretsi zure pasahitza jarraitzeko.',
            'validation_attribute' => 'uneko pasahitza',
        ],

        'actions' => [

            'save' => [
                'label' => 'Gorde aldaketak',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Bi faktoreko autentifikazioa (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Helbide elektronikoaren aldaketa-eskaera bidalita',
            'body' => 'Zure helbide elektronikoa aldatzeko eskaera :email-ra bidali da. Begiratu zure posta elektronikoa aldaketa egiaztatzeko.',
        ],

        'saved' => [
            'title' => 'Gordeta',
        ],

        'throttled' => [
            'title' => 'Eskaera gehiegi. Mesedez, saiatu berriro :seconds segundu barru.',
            'body' => 'Mesedez, saiatu berriro :seconds segundu barru.',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Utzi',
        ],

    ],

];
