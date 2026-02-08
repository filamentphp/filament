<?php

return [

    'title' => 'Atkurti slaptažodį',

    'heading' => 'Pamiršote slaptažodį?',

    'actions' => [

        'login' => [
            'label' => 'grįžti į prisijungimą',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'El. paštas',
        ],

        'actions' => [

            'request' => [
                'label' => 'Siųsti',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'body' => 'Jei jūsų paskyra neegzistuoja, el. laiško negausite.',
        ],

        'throttled' => [
            'title' => 'Per daug bandymų',
            'body' => 'Bandykite dar kartą už :seconds sekundžių.',
        ],

    ],

];
