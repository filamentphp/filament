<?php

return [

    'column_manager' => [

        'heading' => 'Kolonner',

        'actions' => [

            'apply' => [
                'label' => 'Bruk kolonner',
            ],

            'reset' => [
                'label' => 'Nullstill',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Handling|Handlinger',
        ],

        'select' => [

            'loading_message' => 'Laster...',

            'no_search_results_message' => 'Ingen alternativer samsvarer med søket ditt.',

            'placeholder' => 'Velg et alternativ',

            'searching_message' => 'Søker...',

            'search_prompt' => 'Skriv for å søke...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Vis :count mindre',
                'expand_list' => 'Vis :count til',
            ],

            'more_list_items' => 'og :count til',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Velg/fjern alle valgte elementer for massehandlinger.',
        ],

        'bulk_select_record' => [
            'label' => 'Velg/fjern element :key for massehandlinger.',
        ],

        'bulk_select_group' => [
            'label' => 'Velg/fjern gruppen :title for massehandlinger.',
        ],

        'search' => [
            'label' => 'Søk',
            'placeholder' => 'Søk',
            'indicator' => 'Søk',
        ],

    ],

    'summary' => [

        'heading' => 'Oppsummering',

        'subheadings' => [
            'all' => 'Alle :label',
            'group' => ':group oppsummering',
            'page' => 'Denne siden',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Gjennomsnitt',
            ],

            'count' => [
                'label' => 'Tell opp',
            ],

            'sum' => [
                'label' => 'Sum',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Fullfør omorganisering av poster',
        ],

        'enable_reordering' => [
            'label' => 'Omorganiser poster',
        ],

        'filter' => [
            'label' => 'Filter',
        ],

        'group' => [
            'label' => 'Gruppere',
        ],

        'open_bulk_actions' => [
            'label' => 'Massehandlinger',
        ],

        'column_manager' => [
            'label' => 'Veksle kolonner',
        ],

    ],

    'empty' => [

        'heading' => 'Ingen :model',

        'description' => 'Opprett en :model for å komme igang.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Bruk filtre',
            ],

            'remove' => [
                'label' => 'Fjern filter',
            ],

            'remove_all' => [
                'label' => 'Fjern alle filtre',
                'tooltip' => 'Fjern alle filtre',
            ],

            'reset' => [
                'label' => 'Nullstill',
            ],

        ],

        'heading' => 'Filtre',

        'indicator' => 'Aktive filtre',

        'multi_select' => [
            'placeholder' => 'Alle',
        ],

        'select' => [

            'placeholder' => 'Alle',

            'relationship' => [
                'empty_option_label' => 'Ingen',
            ],

        ],

        'trashed' => [

            'label' => 'Slettede poster',

            'only_trashed' => 'Bare slettede poster',

            'with_trashed' => 'Med slettede poster',

            'without_trashed' => 'Uten slettede poster',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Grupper etter',
            ],

            'direction' => [

                'label' => 'Grupperetning',

                'options' => [
                    'asc' => 'Stigende',
                    'desc' => 'Synkende',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Dra og slipp postene i rekkefølge.',

    'selection_indicator' => [

        'selected_count' => '1 post valgt|:count poster valgt',

        'actions' => [

            'select_all' => [
                'label' => 'Velg alle :count',
            ],

            'deselect_all' => [
                'label' => 'Fjern alle markeringer',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sorter etter',
            ],

            'direction' => [

                'label' => 'Sorteringsretning',

                'options' => [
                    'asc' => 'Stigende',
                    'desc' => 'Synkende',
                ],

            ],

        ],

    ],

    'default_model_label' => 'post',

];
