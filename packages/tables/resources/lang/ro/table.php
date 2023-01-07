<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'si alte :count',
        ],

        'messages' => [
            'copied' => 'Copiat',
        ],

    ],

    'fields' => [

        'search_query' => [
            'label' => 'Căutare',
            'placeholder' => 'Căutare',
        ],

    ],

    'pagination' => [

        'label' => 'Navigare',

        'overview' => 'Afișare :first-:last din :total rezultate',

        'fields' => [

            'records_per_page' => [

                'label' => 'pe pagină',

                'options' => [
                    'all' => 'Toate',
                ],

            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Mergi la pagina :page',
            ],

            'next' => [
                'label' => 'Pagina următoare',
            ],

            'previous' => [
                'label' => 'Pagina precedentă',
            ],

        ],

    ],

    'buttons' => [

        'disable_reordering' => [
            'label' => 'Dezactivați reordonarea',
        ],

        'enable_reordering' => [
            'label' => 'Activați reordonarea',
        ],

        'filter' => [
            'label' => 'Filtru',
        ],

        'open_actions' => [
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

        'buttons' => [

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

        'selected_count' => '1 element selectat.|:count elemente selectate.',

        'buttons' => [

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
