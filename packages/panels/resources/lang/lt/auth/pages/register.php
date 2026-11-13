<?php

return [

    'title' => 'Registracija',

    'heading' => 'Registracija',

    'actions' => [

        'login' => [
            'before' => 'arba',
            'label' => 'prisijunkite',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'El. paštas',
        ],

        'name' => [
            'label' => 'Vardas',
        ],

        'password' => [
            'label' => 'Slaptažodis',
            'validation_attribute' => 'slaptažodis',
        ],

        'password_confirmation' => [
            'label' => 'Patvirtinkite slaptažodį',
        ],

        'actions' => [

            'register' => [
                'label' => 'Registruotis',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Per daug registracijos bandymų',
            'body' => 'Pabandykite dar kartą po :seconds sekundžių.',
        ],

    ],

];
