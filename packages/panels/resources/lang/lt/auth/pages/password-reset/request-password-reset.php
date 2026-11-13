<?php

return [

    'title' => 'Atkurti slaptažodį',

    'heading' => 'Pamiršote slaptažodį?',

    'actions' => [

        'login' => [
            'label' => 'grįžti į prisijungimo puslapį',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'El. pašto adresas',
        ],

        'actions' => [

            'request' => [
                'label' => 'Siųsti el. laišką',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'body' => 'Jei jūsų paskyra neegzistuoja, el. laiško negausite.',
        ],

        'throttled' => [
            'title' => 'Per daug užklausų',
            'body' => 'Bandykite dar kartą po :seconds sekundžių.',
        ],

    ],

];
