<?php

return [

    'title' => 'Taasta oma parool',

    'heading' => 'Taasta oma parool',

    'form' => [

        'email' => [
            'label' => 'E-posti aadress',
        ],

        'password' => [
            'label' => 'Parool',
            'validation_attribute' => 'parool',
        ],

        'password_confirmation' => [
            'label' => 'Kinnita parool',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Taasta parool',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Liiga palju taastamiskatseid',
            'body' => 'Palun proovige uuesti :seconds sekundi pärast.',
        ],

    ],

];
