<?php

return [

    'single' => [

        'label' => 'Restaurar',

        'modal' => [

            'heading' => 'Restaurar :label',

            'actions' => [

                'restore' => [
                    'label' => 'Restaurar',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Registre restaurat',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Restaurar seleccionats',

        'modal' => [

            'heading' => 'Restaurar els :label seleccionats',

            'actions' => [

                'restore' => [
                    'label' => 'Restaurar',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Registres restaurats',
            ],

            'restored_partial' => [
                'title' => 'Restaurats :count de :total',
                'missing_authorization_failure_message' => 'No tens permís per restaurar :count.',
                'missing_processing_failure_message' => ':count no s\'han pogut restaurar.',
            ],

            'restored_none' => [
                'title' => 'Cap registre restaurat',
                'missing_authorization_failure_message' => 'No tens permís per restaurar :count.',
                'missing_processing_failure_message' => ':count no s\'han pogut restaurar.',
            ],

        ],

    ],

];
