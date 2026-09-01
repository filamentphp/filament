<?php

return [

    'title' => 'Registracija',

    'heading' => 'Registracija',

    'actions' => [

        'login' => [
            'before' => 'ili',
            'label' => 'se prijavite na postojeći nalog',
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
            'label' => 'Potvrdite lozinku',
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
            'body' => 'Pokušajte ponovo za :seconds s.',
        ],

    ],

];
