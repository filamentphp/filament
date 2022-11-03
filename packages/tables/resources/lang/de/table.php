<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'und :count weitere',
        ],

    ],

    'fields' => [

        'search_query' => [
            'label' => 'Suche',
            'placeholder' => 'Suche',
        ],

    ],

    'pagination' => [

        'label' => 'Seitennavigation',

        'overview' => ':first bis :last von :total Ergebnissen',

        'fields' => [

            'records_per_page' => [

                'label' => 'pro Seite',

                'options' => [
                    'all' => 'Alle',
                ],

            ],

        ],

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

    ],

    'buttons' => [

        'disable_reordering' => [
            'label' => 'Sortieren beenden',
        ],

        'enable_reordering' => [
            'label' => 'Einträge sortieren',
        ],

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

    'filters' => [

        'buttons' => [

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

        'indicator' => 'Aktive Filter',

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

    'reorder_indicator' => 'Zum Sortieren die Einträge per Drag & Drop in die richtige Reihenfolge ziehen.',

    'selection_indicator' => [

        'selected_count' => '1 Datensatz ausgewählt.|:count Datensätze ausgewählt.',

        'buttons' => [

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

];
