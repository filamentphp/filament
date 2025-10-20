<?php

return [

    'title' => 'Registrering',

    'heading' => 'Registrer',

    'actions' => [

        'login' => [
            'before' => 'eller',
            'label' => 'logg inn på konto',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-postadresse',
        ],

        'name' => [
            'label' => 'Navn',
        ],

        'password' => [
            'label' => 'Passord',
            'validation_attribute' => 'passord',
        ],

        'password_confirmation' => [
            'label' => 'Bekreft passord',
        ],

        'actions' => [

            'register' => [
                'label' => 'Registrer',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'For mange forsøk på registrering',
            'body' => 'Vennligst prøv igjen om :seconds sekunder.',
        ],

    ],

];
