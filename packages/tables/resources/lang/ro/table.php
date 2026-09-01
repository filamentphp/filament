<?php

return [

    'column_manager' => [

        'heading' => 'Coloane',

        'actions' => [

            'apply' => [
                'label' => 'Aplică coloane',
            ],

            'reset' => [
                'label' => 'Resetare',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Acțiune|Acțiuni',
        ],

        'select' => [

            'loading_message' => 'Se încarcă...',

            'no_options_message' => 'Nu sunt disponibile opțiuni.',

            'no_search_results_message' => 'Nicio opțiune nu corespunde căutării.',

            'placeholder' => 'Selectați o opțiune',

            'searching_message' => 'Se caută...',

            'search_prompt' => 'Începeți să tastați pentru a căuta...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Afișează cu :count mai puțin',
                'expand_list' => 'Afișează cu :count mai mult',
            ],

            'more_list_items' => 'și alte :count',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Selectați/Deselectați tot pentru operațiuni în masă.',
        ],

        'bulk_select_record' => [
            'label' => 'Selectează/Deselectează elementul :key pentru operațiuni în masă.',
        ],

        'bulk_select_group' => [
            'label' => 'Selectează/Deselectează grupul :title pentru operațiuni în masă.',
        ],

        'search' => [
            'label' => 'Căutare',
            'placeholder' => 'Căutare',
            'indicator' => 'Căutare',
        ],

    ],

    'summary' => [

        'heading' => 'Sumar',

        'subheadings' => [
            'all' => 'Toate :label',
            'group' => 'Sumar :group',
            'page' => 'Această pagină',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Medie',
            ],

            'count' => [
                'label' => 'Numărare',
            ],

            'sum' => [
                'label' => 'Sumă',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Finalizează reordonarea înregistrărilor',
        ],

        'enable_reordering' => [
            'label' => 'Reordonează înregistrările',
        ],

        'filter' => [
            'label' => 'Filtru',
        ],

        'group' => [
            'label' => 'Grupare',
        ],

        'open_bulk_actions' => [
            'label' => 'Operațiuni în masă',
        ],

        'column_manager' => [
            'label' => 'Manager coloane',
        ],

    ],

    'empty' => [

        'heading' => 'Niciun :model',

        'description' => 'Creează un :model pentru a începe.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Aplică filtrele',
            ],

            'remove' => [
                'label' => 'Elimină filtrul',
            ],

            'remove_all' => [
                'label' => 'Elimină toate filtrele',
                'tooltip' => 'Elimină toate filtrele',
            ],

            'reset' => [
                'label' => 'Resetare',
            ],

        ],

        'heading' => 'Filtre',

        'indicator' => 'Filtre active',

        'multi_select' => [
            'placeholder' => 'Toate',
        ],

        'select' => [

            'placeholder' => 'Toate',

            'relationship' => [
                'empty_option_label' => 'Niciunul',
            ],

        ],

        'trashed' => [

            'label' => 'Înregistrări șterse',

            'only_trashed' => 'Doar înregistrările șterse',

            'with_trashed' => 'Cu înregistrări șterse',

            'without_trashed' => 'Fără înregistrări șterse',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Grupează după',
            ],

            'direction' => [

                'label' => 'Direcție grupare',

                'options' => [
                    'asc' => 'Ascendentă',
                    'desc' => 'Descendentă',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Trageți și plasați înregistrările în ordinea dorită.',

    'selection_indicator' => [

        'selected_count' => '1 înregistrare selectată|:count înregistrări selectate',

        'actions' => [

            'select_all' => [
                'label' => 'Selectează toate :count',
            ],

            'deselect_all' => [
                'label' => 'Deselectează toate',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sortează după',
            ],

            'direction' => [

                'label' => 'Direcție sortare',

                'options' => [
                    'asc' => 'Ascendentă',
                    'desc' => 'Descendentă',
                ],

            ],

        ],

    ],

    'default_model_label' => 'înregistrare',

];
