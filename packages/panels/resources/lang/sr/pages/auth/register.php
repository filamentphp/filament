<?php

return [

    'title' => 'Registracija',

    'heading' => 'Registruj se',

    'actions' => [

        'login' => [
            'before' => 'ili',
            'label' => 'se prijavi',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email adresa',
        ],

        'name' => [
            'label' => 'Ime',
        ],

        'password' => [
            'label' => 'Lozinka',
            'validation_attribute' => 'lozinka',
        ],

        'password_confirmation' => [
            'label' => 'Potvrdi lozinku',
        ],

        'actions' => [

            'register' => [
                'label' => 'Registruj se',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Previše pokušaja registracije',
            'body' => 'Molim pokušajte ponovno za :seconds sekundi.',
        ],

    ],

];
