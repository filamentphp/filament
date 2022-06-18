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

        'messages' => [
            'deleted' => 'Registro eliminado',
        ],

    ],

    'multiple' => [

        'label' => 'Forzar el borrado de los elementos seleccionados',

        'modal' => [

            'heading' => 'Forzar eliminación de los :label seleccionados',

            'actions' => [

                'delete' => [
                    'label' => 'Eliminar',
                ],

            ],

        ],

        'messages' => [
            'deleted' => 'Registros eliminados',
        ],

    ],

];
