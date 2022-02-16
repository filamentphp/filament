<?php

return [

    'breadcrumb' => 'Listado',

    'actions' => [

        'create' => [
            'label' => 'Nuevo :label',
            'modal' => [

                'heading' => 'Nuevo :label',

                'actions' => [

                    'create' => [
                        'label' => 'Nuevo',
                    ],

                    'create_and_create_another' => [
                        'label' => 'Guardar & crear otro',
                    ],

                ],
        ],
            'messages' => [
                'created' => 'Registro creado',
            ],

        ],

    ],
    'table' => [

        'actions' => [
            'delete' => [

                'label' => 'Borrar',

                'messages' => [
                    'deleted' => 'Registro borrado',
                ],

            ],
            'edit' => [
                'label' => 'Editar',
                'modal' => [

                    'heading' => 'Editar :label',

                    'actions' => [

                        'save' => [
                            'label' => 'Guardar',
                        ],

                    ],
                ],
                'messages' => [
                    'saved' => 'Registro actualizado',
                ],
            ],

            'view' => [
                'label' => 'Ver',
            ],

        ],

        'bulk_actions' => [
            'delete' => [
                'label' => 'Borrar seleccionado',
                'messages' => [
                    'deleted' => 'Registro(s) borrados',
                ],
            ],

        ],

    ],

];
