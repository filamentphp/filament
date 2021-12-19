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

    'selection_indicator' => [

        'buttons' => [

            'select_all' => [
                'label' => 'Alle :count Datensätze auswählen',
            ],

        ],

    ],

];
