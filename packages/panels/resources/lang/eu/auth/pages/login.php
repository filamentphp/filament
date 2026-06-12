<?php

return [

    'title' => 'Hasi saioa',

    'heading' => 'Hasi saioa',

    'actions' => [

        'register' => [
            'before' => 'edo',
            'label' => 'sortu kontu bat',
        ],

        'request_password_reset' => [
            'label' => 'Pasahitza ahaztu duzu?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Helbide elektronikoa',
        ],

        'password' => [
            'label' => 'Pasahitza',
        ],

        'remember' => [
            'label' => 'Gogoratu nazazu',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Hasi saioa',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Egiaztatu zure identitatea',

        'subheading' => 'Saioa hasten jarraitzeko, zure identitatea egiaztatu behar duzu.',

        'form' => [

            'provider' => [
                'label' => 'Nola nahi duzu egiaztatu?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Berretsi saio-hasiera',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Kredentzial hauek ez datoz bat gure erregistroekin.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Saio-hasiera saiakera gehiegi',
            'body' => 'Mesedez, saiatu berriro :seconds segundu barru.',
        ],

    ],

];
