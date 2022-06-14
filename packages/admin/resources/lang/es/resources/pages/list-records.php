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
                        'label' => 'Guardar y crear otro',
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

                'modal' => [
                    'heading' => 'Borrar :label',
                ],

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

                'modal' => [

                    'heading' => 'Ver :label',

                    'actions' => [

                        'close' => [
                            'label' => 'Cerrar',
                        ],

                    ],

                ],

            ],

        ],

        'bulk_actions' => [

            'delete' => [

                'label' => 'Borrar seleccionado(s)',

                'messages' => [
                    'deleted' => 'Registro(s) borrado(s)',
                ],

            ],

        ],

    ],

];
