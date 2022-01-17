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

    ],

    'empty' => [
        'heading' => 'Sem registros',
    ],

    'filters' => [

        'buttons' => [

            'reset' => [
                'label' => 'Limpar filtros',
            ],

        ],

        'multi_select' => [
            'placeholder' => 'Todos',
        ],

        'select' => [
            'placeholder' => 'Todos',
        ],

    ],

    'selection_indicator' => [

        'selected_count' => '1 registro selecionado.|:count registros selecionados.',

        'buttons' => [

            'select_all' => [
                'label' => 'Selecione todos os :count',
            ],

            'deselect_all' => [
                'label' => 'Desselecionar todos',
            ],

        ],

    ],

];
