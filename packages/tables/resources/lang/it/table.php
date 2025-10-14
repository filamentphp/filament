<?php

return [

    'column_manager' => [

        'heading' => 'Colonne',

        'actions' => [
            'apply' => [
                'label' => 'Applica colonne',
            ],
            'reset' => [
                'label' => 'Reimposta',
            ],
        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Azione|Azioni',
        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Mostra :count di meno',
                'expand_list' => 'Mostra :count di più',
            ],

            'more_list_items' => 'e altri :count',
        ],

        'select' => [
            'loading_message' => 'Caricamento...',
            'no_search_results_message' => 'Nessuna opzione corrisponde alla tua ricerca.',
            'placeholder' => "Seleziona un'opzione",
            'searching_message' => 'Ricerca...',
            'search_prompt' => 'Digita per cercare...',
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

        'column_manager' => [
            'label' => 'Mostra/Nascondi colonne',
        ],

    ],

    'empty' => [

        'heading' => 'Nessun risultato',

        'description' => 'Crea un :model per iniziare.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Applica filtri',
            ],

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
            'relationship' => [
                'empty_option_label' => 'Nessuno',
            ],
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

    'default_model_label' => 'record',

];
