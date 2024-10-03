<?php

return [

    'column_toggle' => [

        'heading' => 'Spalten',

    ],

    'columns' => [

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

        'filter' => [
            'label' => 'Filtern',
        ],

        'group' => [
            'label' => 'Gruppe',
        ],

        'open_bulk_actions' => [
            'label' => 'Aktionen öffnen',
        ],

        'toggle_columns' => [
            'label' => 'Spalten auswählen',
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
                'placeholder' => 'Gruppieren nach',
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

    'reorder_indicator' => 'Zum Sortieren die Einträge per Drag & Drop in die richtige Reihenfolge ziehen.',

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

];
