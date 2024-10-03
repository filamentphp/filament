<?php

return [

    'title' => 'Prisijungti',

    'heading' => 'Prisijunkite prie savo paskyros',

    'actions' => [

        'register' => [
            'before' => 'arba',
            'label' => 'užsiregistruokite',
        ],

        'request_password_reset' => [
            'label' => 'Pamiršote slaptažodį?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'El. paštas',
        ],

        'password' => [
            'label' => 'Slaptažodis',
        ],

        'remember' => [
            'label' => 'Prisiminti mane',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Prisijungti',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Neteisingi prisijungimo duomenys.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Per daug bandymų prisijungti. Bandykite po :seconds sekundžių.',
            'body' => 'Pabandykite dar katą už :seconds sekundžių.',
        ],

    ],

];
