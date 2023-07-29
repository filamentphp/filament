<?php

return [

    'columns' => [

        'text' => [
            'more_list_items' => 'und :count weitere',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Alle Einträge für Massenaktion auswählen/abwählen.',
        ],

        'bulk_select_record' => [
            'label' => 'Eintrag :key für Massenaktion auswählen/abwählen.',
        ],

        'search' => [
            'label' => 'Suche',
            'placeholder' => 'Suche',
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

        'open_bulk_actions' => [
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

        'actions' => [

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
