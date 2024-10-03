<?php

return [

    'title' => 'Nulstil din adgangskode',

    'heading' => 'Nulstil din adgangskode',

    'form' => [

        'email' => [
            'label' => 'E-mail',
        ],

        'password' => [
            'label' => 'Adgangskode',
            'validation_attribute' => 'Adgangskode',
        ],

        'password_confirmation' => [
            'label' => 'Bekræft adgangskode',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Nulstil adgangskode',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'For mange forsøg på nulstilling',
            'body' => 'Prøv igen om :seconds sekunder.',
        ],

    ],

];
