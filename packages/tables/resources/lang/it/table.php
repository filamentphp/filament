<?php

return [

    'columns' => [

        'color' => [

            'messages' => [
                'copied' => 'Copiato',
            ],

        ],

        'tags' => [
            'more' => 'e altri :count',
        ],

    ],

    'fields' => [

        'search_query' => [
            'label' => 'Cerca',
            'placeholder' => 'Cerca',
        ],

    ],

    'pagination' => [

        'label' => 'Navigazione paginazione',

        'overview' => 'Mostrati :first a :last di :total risultati',

        'fields' => [

            'records_per_page' => [

                'label' => 'per pagina',

                'options' => [
                    'all' => 'Tutti',
                ],

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

        'disable_reordering' => [
            'label' => 'Termina riordino records',
        ],

        'enable_reordering' => [
            'label' => 'Riordina records',
        ],

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

            'remove' => [
                'label' => 'Rimuovi filtro',
            ],

            'remove_all' => [
                'label' => 'Rimuovi tutti i filtri',
                'tooltip' => 'Rimuovi tutti i filtri',
            ],

            'reset' => [
                'label' => 'Azzera filtri',
            ],

        ],

        'indicator' => 'Filtri attivi',

        'multi_select' => [
            'placeholder' => 'Tutti',
        ],

        'select' => [
            'placeholder' => 'Tutti',
        ],

        'trashed' => [

            'label' => 'Records eliminati',

            'only_trashed' => 'Solo records eliminati',

            'with_trashed' => 'Con records eliminati',

            'without_trashed' => 'Senza records eliminati',

        ],

    ],

    'reorder_indicator' => 'Prendi e trascina i record in ordine.',

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

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Ordina per',
            ],

            'direction' => [

                'label' => 'Ordine',

                'options' => [
                    'asc' => 'Crescente',
                    'desc' => 'Decrescente',
                ],

            ],

        ],

    ],

];
