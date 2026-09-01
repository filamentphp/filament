<?php

return [

    'single' => [

        'label' => 'Forzar borrado',

        'modal' => [

            'heading' => 'Forzar el borrado de :label',

            'actions' => [

                'delete' => [
                    'label' => 'Eliminar',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Registro eliminado',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Forzar la eliminación de los elementos seleccionados',

        'modal' => [

            'heading' => 'Forzar la eliminación de los :label seleccionados',

            'actions' => [

                'delete' => [
                    'label' => 'Eliminar',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Registros eliminados',
            ],

            'deleted_partial' => [
                'title' => 'Borrados :count de :total',
                'missing_authorization_failure_message' => 'Usted no tiene permiso para eliminar :count.',
                'missing_processing_failure_message' => ':count no se pudieron eliminar.',
            ],

            'deleted_none' => [
                'title' => 'No se pudo eliminar',
                'missing_authorization_failure_message' => 'Usted no tiene permiso para eliminar :count.',
                'missing_processing_failure_message' => ':count no se pudieron eliminar.',
            ],

        ],

    ],

];
