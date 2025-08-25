<?php

return [

    'single' => [

        'label' => 'Eliminar permanentemente',

        'modal' => [

            'heading' => 'Eliminar permanentemente :label',

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

        'label' => 'Eliminar permanentemente seleccionados',

        'modal' => [

            'heading' => 'Eliminar permanentemente :label seleccionados',

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
