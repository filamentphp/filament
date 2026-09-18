<?php

return [

    'title' => 'Weka upya nenosiri lako',

    'heading' => 'Weka upya nenosiri lako',

    'form' => [
        'email' => [
            'label' => 'Barua pepe',
        ],

        'password' => [
            'label' => 'Nenosiri',
            'validation_attribute' => 'nenosiri',
        ],

        'password_confirmation' => [
            'label' => 'Thibitisha nenosiri',
        ],

        'actions' => [
            'reset' => [
                'label' => 'Weka upya nenosiri',
            ],
        ],
    ],

    'notifications' => [
        'throttled' => [
            'title' => 'Majaribio mengi mno ya kuweka upya',
            'body' => 'Tafadhali jaribu tena baada ya sekunde :seconds.',
        ],
    ],

];
