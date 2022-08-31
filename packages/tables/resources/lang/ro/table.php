<?php

return [

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

            'reset' => [
                'label' => 'Resetare filtre',
            ],

        ],

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

];
