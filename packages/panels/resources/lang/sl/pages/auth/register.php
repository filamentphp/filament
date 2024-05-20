<?php

return [

    'title' => 'Registracija',

    'heading' => 'Registracija',

    'actions' => [

        'login' => [
            'before' => 'ali',
            'label' => 'prijavite se v svoj račun',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-poštni naslov',
        ],

        'name' => [
            'label' => 'Ime',
        ],

        'password' => [
            'label' => 'Geslo',
            'validation_attribute' => 'geslo',
        ],

        'password_confirmation' => [
            'label' => 'Potrdite geslo',
        ],

        'actions' => [

            'register' => [
                'label' => 'Registracija',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Preveč poskusov registracije',
            'body' => 'Poskusite znova čez :seconds sekund.',
        ],

    ],

];
