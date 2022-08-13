<?php

return [

    'bulk_actions' => [

        'force_delete' => [

            'label' => 'Endgültig löschen',

            'messages' => [
                'deleted' => 'Gelöscht',
            ],

        ],

        'restore' => [

            'label' => 'Wiederherstellen',

            'messages' => [
                'restored' => 'Wiederhergestellt',
            ],

        ],

    ],

    'buttons' => [

        'filter' => [
            'label' => 'Filtern',
        ],

        'open_actions' => [
            'label' => 'Aktionen öffnen',
        ],

        'toggle_columns' => [
            'label' => 'Spalten auswählen',
        ],

    ],

    'empty' => [
        'heading' => 'Keine Datensätze gefunden',
    ],

    'fields' => [

        'search_query' => [
            'label' => 'Suche',
            'placeholder' => 'Suche',
        ],

    ],

    'filters' => [

        'buttons' => [

            'close' => [
                'label' => 'Schließen',
            ],

            'reset' => [
                'label' => 'Filter zurücksetzen',
            ],

        ],

        'multi_select' => [
            'placeholder' => 'Alle',
        ],

        'select' => [
            'placeholder' => 'Alle',
        ],

        'trashed' => [
            'label' => 'Gelöschte Einträge',
            'only_trashed' => 'Nur gelöschte Einträge',
            'with_trashed' => 'Mit gelöschten Einträgen',
            'without_trashed' => 'Ohne gelöschte Einträge',
        ],

    ],

    'pagination' => [

        'buttons' => [

            'go_to_page' => [
                'label' => 'Weiter zur Seite :page',
            ],

            'next' => [
                'label' => 'Nächste',
            ],

            'previous' => [
                'label' => 'Vorherige',
            ],

        ],

        'fields' => [

            'records_per_page' => [
                'label' => 'pro Seite',
            ],

        ],

        'label' => 'Seitennavigation',

        'overview' => ':first bis :last von :total Ergebnissen',

    ],

    'reorder_indicator' => 'Zum Sortieren die Datensätze per Drag & Drop in die richtige Reihenfolge ziehen.',

    'selection_indicator' => [

        'buttons' => [

            'deselect_all' => [
                'label' => 'Auswahl aufheben',
            ],

            'select_all' => [
                'label' => 'Alle :count Datensätze auswählen',
            ],

        ],

        'selected_count' => '1 Datensatz ausgewählt.|:count Datensätze ausgewählt.',
    ],

];
