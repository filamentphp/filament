<?php

return [

    'title' => 'Jisajili',

    'heading' => 'Jisajili',

    'actions' => [
        'login' => [
            'before' => 'au',
            'label' => 'ingia kwenye akaunti yako',
        ],
    ],

    'form' => [
        'email' => [
            'label' => 'Barua pepe',
        ],

        'name' => [
            'label' => 'Jina',
        ],

        'password' => [
            'label' => 'Nenosiri',
            'validation_attribute' => 'nenosiri',
        ],

        'password_confirmation' => [
            'label' => 'Thibitisha nenosiri',
        ],

        'actions' => [
            'register' => [
                'label' => 'Jisajili',
            ],
        ],
    ],

    'notifications' => [
        'throttled' => [
            'title' => 'Majaribio mengi mno ya kujisajili',
            'body' => 'Tafadhali jaribu tena baada ya sekunde :seconds.',
        ],
    ],

];
