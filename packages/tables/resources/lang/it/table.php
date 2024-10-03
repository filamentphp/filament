<?php

return [

    'column_toggle' => [

        'heading' => 'Colonne',

    ],

    'columns' => [

        'text' => [

            'actions' => [
                'collapse_list' => 'Mostra :count di meno',
                'expand_list' => 'Mostra :count di più',
            ],

            'more_list_items' => 'e altri :count',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Seleziona/Deseleziona tutti gli elementi per le azioni di massa.',
        ],

        'bulk_select_record' => [
            'label' => "Seleziona/Deseleziona l'elemento :key per le azioni di massa.",
        ],

        'bulk_select_group' => [
            'label' => 'Seleziona/deseleziona gruppo :title per azioni collettive.',
        ],

        'search' => [
            'label' => 'Cerca',
            'placeholder' => 'Cerca',
            'indicator' => 'Cerca',
        ],

    ],

    'summary' => [

        'heading' => 'Riepilogo',

        'subheadings' => [
            'all' => 'Tutti gli :label',
            'group' => 'Riepilogo :group',
            'page' => 'Questa pagina',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Media',
            ],

            'count' => [
                'label' => 'Conteggio',
            ],

            'sum' => [
                'label' => 'Somma',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Termina riordino record',
        ],

        'enable_reordering' => [
            'label' => 'Riordina record',
        ],

        'filter' => [
            'label' => 'Filtro',
        ],

        'group' => [
            'label' => 'Gruppo',
        ],

        'open_bulk_actions' => [
            'label' => 'Azioni',
        ],

        'toggle_columns' => [
            'label' => 'Mostra/Nascondi colonne',
        ],

    ],

    'empty' => [

        'heading' => 'Nessun risultato',

        'description' => 'Crea un :model per iniziare.',

    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => 'Rimuovi filtro',
            ],

            'remove_all' => [
                'label' => 'Rimuovi tutti i filtri',
                'tooltip' => 'Rimuovi tutti i filtri',
            ],

            'reset' => [
                'label' => 'Reimposta',
            ],

        ],

        'heading' => 'Filtri',

        'indicator' => 'Filtri attivi',

        'multi_select' => [
            'placeholder' => 'Tutti',
        ],

        'select' => [
            'placeholder' => 'Tutti',
        ],

        'trashed' => [

            'label' => 'Record eliminati',

            'only_trashed' => 'Solo record eliminati',

            'with_trashed' => 'Con record eliminati',

            'without_trashed' => 'Senza record eliminati',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Raggruppa per',
                'placeholder' => 'Raggruppa per',
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

    'reorder_indicator' => 'Trascina e rilascia i record in ordine.',

    'selection_indicator' => [

        'selected_count' => '1 record selezionato|:count record selezionati',

        'actions' => [

            'select_all' => [
                'label' => 'Seleziona tutti :count',
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
