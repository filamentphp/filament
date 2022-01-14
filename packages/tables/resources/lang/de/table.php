<?php

return [

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

        'filter' => [
            'label' => 'Filtern',
        ],

        'open_actions' => [
            'label' => 'Aktionen öffnen',
        ],

    ],

    'actions' => [

        'modal' => [

            'requires_confirmation_subheading' => 'Sind Sie sicher, dass Sie dies tun möchten?',

            'buttons' => [

                'cancel' => [
                    'label' => 'Abbrechen',
                ],

                'confirm' => [
                    'label' => 'Bestätigen',
                ],

                'submit' => [
                    'label' => 'Absenden',
                ],

            ],

        ],

    ],

    'empty' => [
        'heading' => 'Keine Datensätze gefunden',
    ],

    'filters' => [

        'buttons' => [

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

    ],

    'selection_indicator' => [

        'selected_count' => '1 Datensatz ausgewählt ausgewählt.|:count Datensätze ausgewählt.',

        'buttons' => [

            'select_all' => [
                'label' => 'Alle :count Datensätze auswählen',
            ],

            'deselect_all' => [
                'label' => 'Auswahl aufheben',
            ],

        ],

    ],

];
