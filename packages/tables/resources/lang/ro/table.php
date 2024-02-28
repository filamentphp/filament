<?php

return [

    'column_toggle' => [

        'heading' => 'Coloane',

    ],

    'columns' => [

        'text' => [
            'more_list_items' => 'si alte :count',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Selectați/Deselectați tot pentru operațiuni in masă.',
        ],

        'bulk_select_record' => [
            'label' => 'Selectează/Deselectează elementul :key pentru operațiuni in masă.',
        ],

        'bulk_select_group' => [
            'label' => 'Selectează/Deselectează grupul :title pentru operațiuni in masă.',
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
                'label' => 'Suma',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Dezactivați reordonarea',
        ],

        'enable_reordering' => [
            'label' => 'Activați reordonarea',
        ],

        'filter' => [
            'label' => 'Filtru',
        ],

        'group' => [
            'label' => 'Grupare',
        ],

        'open_bulk_actions' => [
            'label' => 'Operațiuni in masă',
        ],

        'toggle_columns' => [
            'label' => 'Deschide/închide coloane',
        ],

    ],

    'empty' => [

        'heading' => 'Nu s-au găsit rezultate',

        'description' => 'Creează un :model pentru a începe.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Aplică filtrele',
            ],

            'remove' => [
                'label' => 'Elimină filtru',
            ],

            'remove_all' => [
                'label' => 'Elimină toate filtrele',
                'tooltip' => 'Elimină toate filtrele',
            ],

            'reset' => [
                'label' => 'Resetare filtre',
            ],

        ],

        'heading' => 'Filtre',

        'indicator' => 'Filtre active',

        'multi_select' => [
            'placeholder' => 'Toate',
        ],

        'select' => [
            'placeholder' => 'Toate',
        ],

        'trashed' => [

            'label' => 'Elemente șterse',

            'only_trashed' => 'Doar elementele șterse',

            'with_trashed' => 'Include elementele șterse',

            'without_trashed' => 'Doar elementele neșterse',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Grupează după',
                'placeholder' => 'Grupează după',
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

    'reorder_indicator' => 'Trageți și plasați elementele în ordine.',

    'selection_indicator' => [

        'selected_count' => '1 element selectat|:count elemente selectate',

        'actions' => [

            'select_all' => [
                'label' => 'Selectare toate :count',
            ],

            'deselect_all' => [
                'label' => 'Deselectare toate',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sortare după coloană',
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

];
