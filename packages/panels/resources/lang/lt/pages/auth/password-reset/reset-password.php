<?php

return [

    'title' => 'Atstatyti slaptažodį',

    'heading' => 'Atstatyti slaptažodį',

    'form' => [

        'email' => [
            'label' => 'El. paštas',
        ],

        'password' => [
            'label' => 'Slaptažodis',
            'validation_attribute' => 'slaptažodžio',
        ],

        'password_confirmation' => [
            'label' => 'Patvirtinkite slaptažodį',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Atstatyti slaptažodį',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Per daug bandymų',
            'body' => 'Bandykite dar kartą už :seconds sekundžių.',
        ],

    ],

];
