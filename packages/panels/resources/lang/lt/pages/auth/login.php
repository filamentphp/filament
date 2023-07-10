<?php

return [

    'title' => 'Prisijungti',

    'heading' => 'Prisijunkite prie savo paskyros',

    'actions' => [

        'authenticate' => [
            'label' => 'Prisijungti',
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

    ],

    'messages' => [
        'failed' => 'Neteisingi prisijungimo duomenys.',
        'throttled' => 'Per daug bandymų prisijungti. Bandykite po :seconds sekundžių.',
    ],

];
