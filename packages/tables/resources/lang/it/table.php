<?php

return [

    'fields' => [

        'search_query' => [
            'label' => 'Cerca',
            'placeholder' => 'Cerca',
        ],

        'tags' => [
            'more_results' => 'e altri :count',
        ],

    ],

    'pagination' => [

        'label' => 'Navigazione paginazione',

        'overview' => 'Mostrati :first a :last di :total risultati',

        'fields' => [

            'records_per_page' => [
                'label' => 'per pagina',
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Vai a pagina :page',
            ],

            'next' => [
                'label' => 'Successivo',
            ],

            'previous' => [
                'label' => 'Precedente',
            ],

        ],

    ],

    'buttons' => [

        'filter' => [
            'label' => 'Filtra',
        ],

        'open_actions' => [
            'label' => 'Azioni aperte',
        ],

        'toggle_columns' => [
            'label' => 'Alterna colonne',
        ],

    ],

    'empty' => [
        'heading' => 'Nessun valore trovato',
    ],

    'filters' => [

        'buttons' => [

            'reset' => [
                'label' => 'Azzera filtri',
            ],

            'close' => [
                'label' => 'Chiudi',
            ],

        ],

        'multi_select' => [
            'placeholder' => 'Tutti',
        ],

        'select' => [
            'placeholder' => 'Tutti',
        ],
    ],

    'selection_indicator' => [
        'selected_count' => '1 record selezionato.|:count records selezionati.',
        'buttons' => [

            'select_all' => [
                'label' => 'Seleziona tutti i :count',
            ],

            'deselect_all' => [
                'label' => 'Deseleziona tutti',
            ],

        ],

    ],

];
