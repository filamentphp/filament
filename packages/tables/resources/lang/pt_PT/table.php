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

        'overview' => 'A mostrar :first a :last de :total resultados',

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

    'empty' => [
        'heading' => 'Sem registos',
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

        'selected_count' => '1 registo selecionado.|:count registos selecionados.',

        'buttons' => [

            'select_all' => [
                'label' => 'Selecionar todos :count',
            ],

            'deselect_all' => [
                'label' => 'Desmarcar todos',
            ],

        ],

    ],

];
