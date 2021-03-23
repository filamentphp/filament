<?php

return [

    'buttons' => [

        'attach' => [
            'label' => 'Vincular Existente',
        ],

        'create' => [
            'label' => 'Nuevo',
        ],

        'detach' => [
            'label' => 'Desvincular seleccionado',
        ],

    ],

    'modals' => [

        'attach' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Cancelar',
                ],

                'attach' => [
                    'label' => 'Vincular',
                ],

                'attachAnother' => [
                    'label' => 'Vincular y Vincular Otro',
                ],

            ],

            'form' => [

                'related' => [
                    'placeholder' => 'Escribe para buscar...',
                ],

            ],

            'heading' => 'Vincular Existente',

            'messages' => [
                'attached' => '¡Vinculado!',
            ],

        ],

        'create' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Cancelar',
                ],

                'create' => [
                    'label' => 'Crear',
                ],

                'createAnother' => [
                    'label' => 'Crear y Crear Otro',
                ],

            ],

            'heading' => 'Crear',

            'messages' => [
                'created' => '¡Creado!',
            ],

        ],

        'detach' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Cancelar',
                ],

                'detach' => [
                    'label' => 'Desvincular seleccionado',
                ],

            ],

            'description' => '¿Está seguro de que desea desvincular los registros seleccionados? Esta acción no se puede deshacer.',

            'heading' => '¿Desvincular registros seleccionados? ',

            'messages' => [
                'detached' => '¡Desvinculado!',
            ],

        ],

        'edit' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Cancelar',
                ],

                'save' => [
                    'label' => 'Guardar',
                ],

            ],

            'heading' => 'Editar',

            'messages' => [
                'saved' => '¡Guardado!',
            ],

        ],

    ],

];
