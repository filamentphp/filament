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
                'title' => 'Registro restaurado',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Restaurar seleccionados',

        'modal' => [

            'heading' => 'Restaurar los :label seleccionados',

            'actions' => [

                'restore' => [
                    'label' => 'Restaurar',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Registros restaurados',
            ],

            'restored_partial' => [
                'title' => 'Restaurados :count de :total',
                'missing_authorization_failure_message' => 'Usted no tiene permiso para restaurar :count.',
                'missing_processing_failure_message' => ':count no se pudieron restaurar.',
            ],

            'restored_none' => [
                'title' => 'Ningún registro restaurado',
                'missing_authorization_failure_message' => 'Usted no tiene permiso para restaurar :count.',
                'missing_processing_failure_message' => ':count no se pudieron restaurar.',
            ],

        ],

    ],

];
