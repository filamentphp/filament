<?php

return [

    'column_manager' => [

        'heading' => 'Colonne',

        'position' => 'Posizione :position di :count.',

        'position_in_group' => 'Posizione :position di :count in :group.',

        'reordered' => ':column spostata in posizione :position di :count.',

        'reordered_deferred' => ':column spostata in posizione :position di :count. Applica le colonne per salvare le modifiche.',

        'reordered_in_group' => ':column spostata in posizione :position di :count in :group.',

        'reordered_in_group_deferred' => ':column spostata in posizione :position di :count in :group. Applica le colonne per salvare le modifiche.',

        'actions' => [

            'apply' => [
                'label' => 'Applica colonne',
            ],

            'move_up' => [
                'label' => 'Sposta su :column',
            ],

            'move_down' => [
                'label' => 'Sposta giù :column',
            ],

            'reorder' => [
                'label' => 'Riordina colonna',
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

        'editable' => [
            'saving' => 'Salvataggio in corso...',
            'saved' => 'Modifica salvata.',
            'failure' => 'Non è stato possibile confermare il salvataggio. Ricarica la pagina prima di riprovare.',
            'unavailable' => 'Questo elemento non è più modificabile.',
        ],

        'icon' => [

            'boolean' => [
                'true' => 'Sì',
                'false' => 'No',
            ],

        ],

        'select' => [

            'loading_message' => 'Caricamento...',

            'no_options_message' => 'Nessuna opzione disponibile.',

            'no_search_results_message' => 'Nessuna opzione corrisponde alla tua ricerca.',

            'placeholder' => "Seleziona un'opzione",

            'searching_message' => 'Ricerca...',

            'search_prompt' => 'Digita per cercare...',

        ],

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
            'all' => 'Tutte le righe',
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

        'reorder_record' => [
            'label' => 'Riordina elemento :key',
        ],

        'move_record_up' => [
            'label' => 'Sposta su :label',
        ],

        'move_record_down' => [
            'label' => 'Sposta giù :label',
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

        'toggle_record_content' => [
            'label' => 'Espandi/comprimi elemento :key',
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

    'loading' => 'Caricamento...',

    'reorder_indicator' => 'Trascina e rilascia i record in ordine.',

    'reordering' => [
        'position' => ':label, posizione :position di :count.',
        'moved' => ':label spostato in posizione :position di :count.',
    ],

    'result_count' => '{0} Nessun risultato|{1} :count risultato|[2,*] :count risultati',

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
