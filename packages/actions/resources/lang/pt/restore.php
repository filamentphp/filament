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
                'title' => 'Restaurado',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Restaurar seleccionados',

        'modal' => [

            'heading' => 'Restaurar :label seleccionados',

            'actions' => [

                'restore' => [
                    'label' => 'Restaurar',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Restaurado',
            ],

            'restored_partial' => [
                'title' => 'Restaurados :count de :total',
                'missing_authorization_failure_message' => 'Não tem permissão para restaurar :count.',
                'missing_processing_failure_message' => ':count não puderam ser restaurados.',
            ],

            'restored_none' => [
                'title' => 'Falha ao restaurar',
                'missing_authorization_failure_message' => 'Não tem permissão para restaurar :count.',
                'missing_processing_failure_message' => ':count não puderam ser restaurados.',
            ],

        ],

    ],

];
