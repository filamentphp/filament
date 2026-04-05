<?php

return [

    'title' => 'Berrezarri zure pasahitza',

    'heading' => 'Berrezarri zure pasahitza',

    'form' => [

        'email' => [
            'label' => 'Helbide elektronikoa',
        ],

        'password' => [
            'label' => 'Pasahitza',
            'validation_attribute' => 'pasahitza',
        ],

        'password_confirmation' => [
            'label' => 'Berretsi pasahitza',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Berrezarri pasahitza',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Berrezartze-saiakera gehiegi',
            'body' => 'Mesedez, saiatu berriro :seconds segundu barru.',
        ],

    ],

];
