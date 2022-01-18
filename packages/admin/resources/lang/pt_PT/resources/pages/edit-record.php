<?php

return [

    'title' => 'Editar :label',

    'breadcrumb' => 'Editar',

    'actions' => [

        'delete' => [

            'label' => 'Remover',

            'modal' => [

                'heading' => 'Remover :label',

                'subheading' => 'Tem certeza que pretende fazer isso?',

                'buttons' => [

                    'delete' => [
                        'label' => 'Remover',
                    ],

                ],

            ],

        ],

        'view' => [
            'label' => 'Mostrar',
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
        'saved' => 'Guardado!',
    ],

];
