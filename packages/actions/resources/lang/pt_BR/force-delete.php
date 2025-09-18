<?php

return [

    'single' => [

        'label' => 'Forçar exclusão',

        'modal' => [

            'heading' => 'Forçar exclusão de :label',

            'actions' => [

                'delete' => [
                    'label' => 'Excluir',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Excluído',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Forçar exclusão selecionado',

        'modal' => [

            'heading' => 'Forçar exclusão de :label selecionado',

            'actions' => [

                'delete' => [
                    'label' => 'Excluir',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Excluído',
            ],

            'deleted_partial' => [
                'title' => 'Excluídos :count de :total',
                'missing_authorization_failure_message' => 'Você não tem permissão para excluir :count.',
                'missing_processing_failure_message' => ':count não pôde(m) ser excluído(s).',
            ],

            'deleted_none' => [
                'title' => 'Falha ao excluir',
                'missing_authorization_failure_message' => 'Você não tem permissão para excluir :count.',
                'missing_processing_failure_message' => ':count não pôde(m) ser excluído(s).',
            ],

        ],

    ],

];
