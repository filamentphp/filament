<?php

return [

    'fields' => [

        'search_query' => [
            'label' => 'Procurar',
            'placeholder' => 'Procurar',
        ],

    ],

    'pagination' => [

        'label' => 'Paginação',

        'overview' => 'Exibindo :first a :last de :total resultados',

        'fields' => [

            'records_per_page' => [
                'label' => 'por página',
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Ir para página :page',
            ],

            'next' => [
                'label' => 'Próximo',
            ],

            'previous' => [
                'label' => 'Anterior',
            ],

        ],

    ],

    'buttons' => [

        'filter' => [
            'label' => 'Filtrar',
        ],

        'open_actions' => [
            'label' => 'Ações abertas',
        ],

    ],

    'actions' => [

        'modal' => [

            'requires_confirmation_subheading' => 'Você tem certeza de que gostaria de fazer isso?',

            'buttons' => [

                'cancel' => [
                    'label' => 'Cancelar',
                ],

                'confirm' => [
                    'label' => 'Confirme',
                ],

                'submit' => [
                    'label' => 'Enviar para',
                ],

            ],

        ],

        'buttons' => [

            'select_all' => [
                'label' => 'Selecione todos os :count registros',
            ],

        ],

    ],

    'empty' => [
        'heading' => 'Sem registros',
    ],

];
