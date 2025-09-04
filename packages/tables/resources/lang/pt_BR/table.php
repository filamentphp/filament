<?php

return [

    'column_manager' => [

        'heading' => 'Colunas',

        'actions' => [

            'apply' => [
                'label' => 'Aplicar colunas',
            ],

            'reset' => [
                'label' => 'Redefinir',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Ação|Ações',
        ],

        'select' => [

            'loading_message' => 'Carregando...',

            'no_search_results_message' => 'Nenhuma opção corresponde à sua busca.',

            'placeholder' => 'Selecione uma opção',

            'searching_message' => 'Buscando...',

            'search_prompt' => 'Digite para buscar...',

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
            'label' => 'Marcar/desmarcar todos os itens para ações em massa.',
        ],

        'bulk_select_record' => [
            'label' => 'Marcar/desmarcar o item :key para ações em massa.',
        ],

        'bulk_select_group' => [
            'label' => 'Marcar/desmarcar o grupo :title para ações em massa.',
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
            'group' => ':group resumo',
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
            'label' => 'Concluir a reordenação de registros',
        ],

        'enable_reordering' => [
            'label' => 'Reordenar registros',
        ],

        'filter' => [
            'label' => 'Filtrar',
        ],

        'group' => [
            'label' => 'Agrupar',
        ],

        'open_bulk_actions' => [
            'label' => 'Abrir ações',
        ],

        'column_manager' => [
            'label' => 'Alternar colunas',
        ],

    ],

    'empty' => [

        'heading' => 'Sem registros',

        'description' => 'Crie um :model para começar.',

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
                'label' => 'Limpar filtros',
            ],

        ],

        'heading' => 'Filtros',

        'indicator' => 'Filtros ativos',

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

            'label' => 'Registros excluídos',

            'only_trashed' => 'Somente registros excluídos',

            'with_trashed' => 'Exibir registros excluídos',

            'without_trashed' => 'Não exibir registros excluídos',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Agrupar por',
            ],

            'direction' => [

                'label' => 'Direção do agrupamento',

                'options' => [
                    'asc' => 'Ascendente',
                    'desc' => 'Descendente',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Arraste e solte os registros na ordem.',

    'selection_indicator' => [

        'selected_count' => '1 registro selecionado|:count registros selecionados',

        'actions' => [

            'select_all' => [
                'label' => 'Selecione todos os :count',
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

                'label' => 'Direção da ordenação',

                'options' => [
                    'asc' => 'Ascendente',
                    'desc' => 'Descendente',
                ],

            ],

        ],

    ],

    'default_model_label' => 'registro',

];
