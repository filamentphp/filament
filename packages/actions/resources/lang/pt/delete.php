<?php

return [

    'single' => [

        'label' => 'Eliminar',

        'modal' => [

            'heading' => 'Eliminar :label',

            'actions' => [

                'delete' => [
                    'label' => 'Eliminar',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Eliminado',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Eliminar seleccionados',

        'modal' => [

            'heading' => 'Eliminar :label seleccionados',

            'actions' => [

                'delete' => [
                    'label' => 'Eliminar',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Eliminado',
            ],

            'deleted_partial' => [
                'title' => 'Eliminados :count de :total',
                'missing_authorization_failure_message' => 'Não tem permissão para eliminar :count.',
                'missing_processing_failure_message' => ':count não puderam ser eliminados.',
            ],

            'deleted_none' => [
                'title' => 'Falha ao eliminar',
                'missing_authorization_failure_message' => 'Não tem permissão para eliminar :count.',
                'missing_processing_failure_message' => ':count não puderam ser eliminados.',
            ],

        ],

    ],

];
