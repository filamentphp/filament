<?php

return [

    'title' => 'Registracija',

    'heading' => 'Registriraj se',

    'actions' => [

        'login' => [
            'before' => 'ili',
            'label' => 'se prijavi',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Adresa e-pošte',
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
                'label' => 'Registriraj se',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Previše pokušaja registracije',
            'body' => 'Molim te, pokušaj ponovno za :seconds sekundi.',
        ],

    ],

];
