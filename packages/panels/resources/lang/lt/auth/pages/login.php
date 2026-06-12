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

    'multi_factor' => [

        'heading' => 'Patvirtinkite savo tapatybę',

        'subheading' => 'Norėdami tęsti prisijungimą, turite patvirtinti savo tapatybę.',

        'form' => [

            'provider' => [
                'label' => 'Kaip norėtumėte patvirtinti?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Patvirtinti prisijungimą',
                ],

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
