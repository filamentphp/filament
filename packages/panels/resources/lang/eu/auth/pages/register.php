<?php

return [

    'title' => 'Eman izena',

    'heading' => 'Sortu kontua',

    'actions' => [

        'login' => [
            'before' => 'edo',
            'label' => 'hasi saioa zure kontuan',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Helbide elektronikoa',
        ],

        'name' => [
            'label' => 'Izena',
        ],

        'password' => [
            'label' => 'Pasahitza',
            'validation_attribute' => 'pasahitza',
        ],

        'password_confirmation' => [
            'label' => 'Berretsi pasahitza',
        ],

        'actions' => [

            'register' => [
                'label' => 'Eman izena',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Erregistro-saiakera gehiegi',
            'body' => 'Mesedez, saiatu berriro :seconds segundu barru.',
        ],

    ],

];
