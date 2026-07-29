<?php

return [

    'column_manager' => [

        'heading' => 'Spalten',

        'actions' => [

            'apply' => [
                'label' => 'Spalten anwenden',
            ],

            'reorder' => [
                'label' => 'Spalte verschieben',
            ],

            'reset' => [
                'label' => 'Zurücksetzen',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Aktion|Aktionen',
        ],

        'icon' => [

            'boolean' => [
                'true' => 'Ja',
                'false' => 'Nein',
            ],

        ],

        'select' => [

            'loading_message' => 'Lädt...',

            'no_options_message' => 'Keine Optionen verfügbar.',

            'no_search_results_message' => 'Keine Optionen entsprechen Ihrer Suche.',

            'placeholder' => 'Option auswählen',

            'searching_message' => 'Sucht...',

            'search_prompt' => 'Beginnen Sie mit der Eingabe, um zu suchen...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => ':count weniger anzeigen',
                'expand_list' => ':count weitere anzeigen',
            ],

            'more_list_items' => 'und :count weitere',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Alle Einträge für Stapelverarbeitung auswählen/abwählen.',
        ],

        'bulk_select_record' => [
            'label' => 'Eintrag :key für Stapelverarbeitung auswählen/abwählen.',
        ],

        'bulk_select_group' => [
            'label' => 'Gruppe auswählen/abwählen :title für Stapelverarbeitung.',
        ],

        'search' => [
            'label' => 'Suche',
            'placeholder' => 'Suche',
            'indicator' => 'Suche',
        ],

    ],

    'summary' => [

        'heading' => 'Zusammenfassung',

        'subheadings' => [
            'all' => 'Alle :label',
            'group' => ':group Zusammenfassung',
            'page' => 'Diese Seite',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Durchschnitt',
            ],

            'count' => [
                'label' => 'Anzahl',
            ],

            'sum' => [
                'label' => 'Summe',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Sortieren beenden',
        ],

        'enable_reordering' => [
            'label' => 'Einträge sortieren',
        ],

        'reorder_record' => [
            'label' => 'Eintrag :key verschieben',
        ],

        'filter' => [
            'label' => 'Filtern',
        ],

        'group' => [
            'label' => 'Gruppe',
        ],

        'open_bulk_actions' => [
            'label' => 'Mehrfachaktionen',
        ],

        'column_manager' => [
            'label' => 'Spalten auswählen',
        ],

        'toggle_record_content' => [
            'label' => 'Eintrag :key aus-/einklappen',
        ],

    ],

    'empty' => [

        'heading' => 'Keine :model',

        'description' => 'Erstelle ein(e) :model um zu beginnen.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => ' Filter anwenden',
            ],

            'remove' => [
                'label' => 'Filter löschen',
            ],

            'remove_all' => [
                'label' => 'Alle Filter löschen',
                'tooltip' => 'Alle Filter löschen',
            ],

            'reset' => [
                'label' => 'Filter zurücksetzen',
            ],

        ],

        'heading' => 'Filter',

        'indicator' => 'Aktive Filter',

        'multi_select' => [
            'placeholder' => 'Alle',
        ],

        'select' => [

            'placeholder' => 'Alle',

            'relationship' => [
                'empty_option_label' => 'Keine',
            ],

        ],

        'trashed' => [

            'label' => 'Gelöschte Einträge',

            'only_trashed' => 'Nur gelöschte Einträge',

            'with_trashed' => 'Mit gelöschten Einträgen',

            'without_trashed' => 'Ohne gelöschte Einträge',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Gruppieren nach',
            ],

            'direction' => [

                'label' => 'Gruppierungsrichtung',

                'options' => [
                    'asc' => 'Aufsteigend',
                    'desc' => 'Absteigend',
                ],

            ],

        ],

    ],

    'loading' => 'Lädt...',

    'reorder_indicator' => 'Zum Sortieren die Einträge per Drag & Drop in die richtige Reihenfolge ziehen.',

    'result_count' => '{0} Keine Ergebnisse|{1} :count Ergebnis|[2,*] :count Ergebnisse',

    'selection_indicator' => [

        'selected_count' => '1 Datensatz ausgewählt|:count Datensätze ausgewählt',

        'actions' => [

            'select_all' => [
                'label' => 'Alle :count Datensätze auswählen',
            ],

            'deselect_all' => [
                'label' => 'Auswahl aufheben',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sortieren nach',
            ],

            'direction' => [

                'label' => 'Sortierrichtung',

                'options' => [
                    'asc' => 'Aufsteigend',
                    'desc' => 'Absteigend',
                ],

            ],

        ],

    ],

    'default_model_label' => 'Datensatz',

];
