<?php

return [

    'column_manager' => [

        'heading' => 'Colunas',

        'actions' => [

            'apply' => [
                'label' => 'Aplicar colunas',
            ],

            'reset' => [
                'label' => 'Repor',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Acção|Acções',
        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Mostrar menos :count',
                'expand_list' => 'Mostrar mais :count',
            ],

            'more_list_items' => 'e mais :count',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Marcar/desmarcar todos os itens para acções em massa.',
        ],

        'bulk_select_record' => [
            'label' => 'Marcar/desmarcar o item :key para acções em massa.',
        ],

        'bulk_select_group' => [
            'label' => 'Marcar/desmarcar o grupo :title para acções em massa.',
        ],

        'search' => [
            'label' => 'Pesquisar',
            'placeholder' => 'Pesquisar',
            'indicator' => 'Pesquisar',
        ],

    ],

    'summary' => [

        'heading' => 'Resumo',

        'subheadings' => [
            'all' => 'Todos :label',
            'group' => 'Resumo de :group',
            'page' => 'Esta página',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Média',
            ],

            'count' => [
                'label' => 'Contagem',
            ],

            'sum' => [
                'label' => 'Soma',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Concluir a reordenação de registos',
        ],

        'enable_reordering' => [
            'label' => 'Reordenar registos',
        ],

        'filter' => [
            'label' => 'Filtrar',
        ],

        'group' => [
            'label' => 'Agrupar',
        ],

        'open_bulk_actions' => [
            'label' => 'Acções em massa',
        ],

        'column_manager' => [
            'label' => 'Activar colunas',
        ],

    ],

    'empty' => [

        'heading' => 'Sem :model',

        'description' => 'Crie um(a) :model para começar.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Aplicar filtros',
            ],

            'remove' => [
                'label' => 'Remover filtro',
            ],

            'remove_all' => [
                'label' => 'Remover todos os filtros',
                'tooltip' => 'Remover todos os filtros',
            ],

            'reset' => [
                'label' => 'Repôr',
            ],

        ],

        'heading' => 'Filtros',

        'indicator' => 'Filtros activos',

        'multi_select' => [
            'placeholder' => 'Todos',
        ],

        'select' => [

            'placeholder' => 'Todos',

            'relationship' => [
                'empty_option_label' => 'Nenhum',
            ],

        ],

        'trashed' => [

            'label' => 'Registos eliminados',

            'only_trashed' => 'Apenas registos eliminados',

            'with_trashed' => 'Mostrar registos eliminados',

            'without_trashed' => 'Não mostrar registos eliminados',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Agrupar por',
                'placeholder' => 'Agrupar por',
            ],

            'direction' => [

                'label' => 'Direcção de agrupamento',

                'options' => [
                    'asc' => 'Ascendente',
                    'desc' => 'Descendente',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Arraste e solte os registos por ordem.',

    'selection_indicator' => [

        'selected_count' => '1 registo seleccionado|:count registos seleccionados',

        'actions' => [

            'select_all' => [
                'label' => 'Seleccionar todos os :count',
            ],

            'deselect_all' => [
                'label' => 'Desmarcar todos',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Ordenar por',
            ],

            'direction' => [

                'label' => 'Direcção de ordenação',

                'options' => [
                    'asc' => 'Ascendente',
                    'desc' => 'Descendente',
                ],

            ],

        ],

    ],

    'default_model_label' => 'registo',

];
