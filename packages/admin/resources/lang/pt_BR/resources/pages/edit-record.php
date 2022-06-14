<?php

return [

    'title' => 'Editar :label',

    'breadcrumb' => 'Editar',

    'actions' => [

        'delete' => [

            'label' => 'Excluir',

            'modal' => [

                'heading' => 'Excluir :label',

                'subheading' => 'Você tem certeza que gostaria de fazer isso?',

                'buttons' => [

                    'delete' => [
                        'label' => 'Excluir',
                    ],

                ],

            ],

            'messages' => [
                'deleted' => 'Excluído!',
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
                'label' => 'Salvar',
            ],

        ],

    ],

    'messages' => [
        'saved' => 'Salvo!',
    ],

];
