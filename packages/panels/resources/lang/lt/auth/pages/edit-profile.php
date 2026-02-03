<?php

return [

    'label' => 'Profilis',

    'form' => [

        'email' => [
            'label' => 'El. paštas',
        ],

        'name' => [
            'label' => 'Vardas',
        ],

        'password' => [
            'label' => 'Naujas slaptažodis',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Patvirtinkite naują slaptažodį',
            'validation_attribute' => 'password confirmation',
        ],

        'current_password' => [
            'label' => 'Dabartinis slaptažodis',
            'below_content' => 'Saugumo sumetimais, prašome patvirtinti savo slaptažodį, kad galėtumėte tęsti.',
            'validation_attribute' => 'current password',
        ],

        'actions' => [

            'save' => [
                'label' => 'Išsaugoti',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Dviejų veiksnių autentifikacija (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'El. pašto adreso keitimo užklausa išsiųsta',
            'body' => 'Užklausa pakeisti jūsų el. pašto adresą buvo išsiųsta į :email. Prašome patikrinti savo el. paštą, kad patvirtintumėte pakeitimą.',
        ],

        'saved' => [
            'title' => 'Išsaugota',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Atgal',
        ],

    ],

];
