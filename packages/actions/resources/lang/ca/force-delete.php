<?php

return [

    'single' => [

        'label' => 'Forçar l\'esborrat',

        'modal' => [

            'heading' => 'Forçar l\'esborrat de :label',

            'actions' => [

                'delete' => [
                    'label' => 'Esborrar',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Registre esborrat',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Forçar l\'esborrat dels elements seleccionats',

        'modal' => [

            'heading' => 'Forçar l\'esborrat dels :label seleccionats',

            'actions' => [

                'delete' => [
                    'label' => 'Esborrar',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Registres esborrats',
            ],

            'deleted_partial' => [
                'title' => 'Esborrats :count de :total',
                'missing_authorization_failure_message' => 'No tens permís per esborrar :count.',
                'missing_processing_failure_message' => ':count no s\'han pogut esborrar.',
            ],

            'deleted_none' => [
                'title' => 'No s\'ha pogut esborrar',
                'missing_authorization_failure_message' => 'No tens permís per esborrar :count.',
                'missing_processing_failure_message' => ':count no s\'han pogut esborrar.',
            ],

        ],

    ],

];
