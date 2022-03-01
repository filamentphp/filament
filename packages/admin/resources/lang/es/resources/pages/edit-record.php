<?php

return [

    'title' => 'Editar :label',

    'breadcrumb' => 'Editar',

    'actions' => [

        'delete' => [

            'label' => 'Borrar',

            'modal' => [

                'heading' => 'Borrar :label',

                'subheading' => '¿Está segura/o de hacer esto?',

                'buttons' => [

                    'delete' => [
                        'label' => 'Borrar',
                    ],

                ],

            ],
            'messages' => [
                'deleted' => 'Borrado',
            ],

        ],

        'view' => [
            'label' => 'Ver',
        ],

    ],

    'form' => [

        'actions' => [

            'cancel' => [
                'label' => 'Cancelar',
            ],

            'save' => [
                'label' => 'Guardar',
            ],

        ],

    ],

    'messages' => [
        'saved' => 'Guardado',
    ],

];
