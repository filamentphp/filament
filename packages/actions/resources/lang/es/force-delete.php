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

        ],

    ],

];
