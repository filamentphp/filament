<?php

return [

    'breadcrumb' => 'Listar',

    'actions' => [

        'create' => [

            'label' => 'Novo :label',

        'modal' => [

            'heading' => 'Novo :label',

            'actions' => [

                'create' => [
                    'label' => 'Criar',
                ],

                'create_and_create_another' => [
                    'label' => 'Criar e criar novo',
                ],

            ],

        ],

        'messages' => [
            'created' => 'Salvo!',
            ],

        ],

    ],

    'table' => [

        'actions' => [

            'delete' => [

                'label' => 'Excluir',

                'messages' => [
                    'deleted' => 'Excluído!',
                ],

            ],

            'edit' => [

                'label' => 'Editar',

                'modal' => [

                    'heading' => 'Editar :label',

                    'actions' => [

                        'save' => [
                            'label' => 'Salvar',
                        ],

                    ],

                ],

                'messages' => [
                    'saved' => 'Salvo!',
                ],

            ],

            'view' => [
                'label' => 'Mostrar',
            ],

        ],

        'bulk_actions' => [

            'delete' => [

                'label' => 'Excluir selecionados',

                'messages' => [
                    'deleted' => 'Excluído!',
                ],

            ],

        ],

    ],

];
