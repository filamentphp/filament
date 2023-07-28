<?php

return [

    'columns' => [

        'text' => [
            'more_list_items' => 'si alte :count',
        ],

    ],

    'fields' => [

        'search' => [
            'label' => 'Căutare',
            'placeholder' => 'Căutare',
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

        'open_bulk_actions' => [
            'label' => 'Desdere operațiuni',
        ],

        'toggle_columns' => [
            'label' => 'Deschide/închide coloane',
        ],

    ],

    'empty' => [
        'heading' => 'Nu s-au găsit rezultate',
    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => 'Ştergere filtru',
            ],

            'remove_all' => [
                'label' => 'Şterge toate filtrele',
                'tooltip' => 'Ştergere toate filtrele',
            ],

            'reset' => [
                'label' => 'Resetare filtre',
            ],

        ],

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

            'with_trashed' => 'Elementele șterse inclusiv',

            'without_trashed' => 'Doar elementele neșterse',

        ],

    ],

    'reorder_indicator' => 'Trageți și plasați înregistrările în ordine.',

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
