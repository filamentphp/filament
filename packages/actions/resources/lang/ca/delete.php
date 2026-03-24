<?php

return [

    'single' => [

        'label' => 'Esborrar',

        'modal' => [

            'heading' => 'Esborrar :label',

            'actions' => [

                'delete' => [
                    'label' => 'Esborrar',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Esborrat',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Esborrar seleccionats',

        'modal' => [

            'heading' => 'Esborrar :label seleccionats',

            'actions' => [

                'delete' => [
                    'label' => 'Esborrar',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Esborrats',
            ],

            'deleted_partial' => [
                'title' => 'Esborrats :count de :total',
                'missing_authorization_failure_message' => 'No tens permís per eliminar :count.',
                'missing_processing_failure_message' => ':count no s\'han pogut eliminar.',
            ],

            'deleted_none' => [
                'title' => 'No s\'ha pogut eliminar',
                'missing_authorization_failure_message' => 'No tens permís per eliminar :count.',
                'missing_processing_failure_message' => ':count no s\'han pogut eliminar.',
            ],

        ],

    ],

];
