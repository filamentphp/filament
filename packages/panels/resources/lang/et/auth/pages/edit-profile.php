<?php

return [

    'label' => 'Profiil',

    'form' => [

        'email' => [
            'label' => 'E-posti aadress',
        ],

        'name' => [
            'label' => 'Nimi',
        ],

        'password' => [
            'label' => 'Uus parool',
            'validation_attribute' => 'parool',
        ],

        'password_confirmation' => [
            'label' => 'Kinnita uus parool',
            'validation_attribute' => 'parooli kinnitamine',
        ],

        'current_password' => [
            'label' => 'Praegune parool',
            'below_content' => 'Turvalisuse huvides palun kinnitage oma parool, et jätkata.',
            'validation_attribute' => 'praegune parool',
        ],

        'actions' => [

            'save' => [
                'label' => 'Salvesta muudatused',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Kahefaktoriline autentimine (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'E-posti aadressi muutmise taotlus saadetud',
            'body' => 'E-posti aadressi muutmise taotlus on saadetud :email. Palun kontrollige oma e-posti, et kinnitada muutmine.',
        ],

        'saved' => [
            'title' => 'Salvestatud',
        ],

        'throttled' => [
            'title' => 'Liiga palju päringuid. Palun proovige uuesti :seconds sekundi pärast.',
            'body' => 'Palun proovige uuesti :seconds sekundi pärast.',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Tühista',
        ],

    ],

];
