<?php

return [

    'column_manager' => [

        'heading' => 'Kolonner',

        'actions' => [

            'apply' => [
                'label' => 'Anvend kolonner',
            ],

            'reset' => [
                'label' => 'Nulstil',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Handling|Handlinger',
        ],

        'select' => [

            'loading_message' => 'Indlæser...',

            'no_search_results_message' => 'Ingen muligheder der matcher din søgning.',

            'placeholder' => 'Vælg en indstilling',

            'searching_message' => 'Søger...',

            'search_prompt' => 'Begynd at skrive for at søge ...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Vis :count mindre',
                'expand_list' => 'Vis :count flere',
            ],

            'more_list_items' => 'og :count flere',
        ],

    ],

    'fields' => [
        'bulk_select_page' => [
            'label' => 'Vælg/fravælg alle rækker for masse handlinger.',
        ],

        'bulk_select_record' => [
            'label' => 'Vælg/fravælg :key for masse handlinger.',
        ],

        'bulk_select_group' => [
            'label' => 'Vælg/fravælg gruppe :title til massehandlinger.',
        ],

        'search' => [
            'label' => 'Søg',
            'placeholder' => 'Søg',
            'indicator' => 'Søg',
        ],

    ],

    'summary' => [

        'heading' => 'Resumé',

        'subheadings' => [
            'all' => 'Alle :label',
            'group' => ':group resumé',
            'page' => 'Denne side',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Gennemsnit',
            ],

            'count' => [
                'label' => 'Antal',
            ],

            'sum' => [
                'label' => 'Sum',
            ],

        ],

    ],

    'actions' => [
        'disable_reordering' => [
            'label' => 'Afslut omrokering',
        ],

        'enable_reordering' => [
            'label' => 'Omroker rækker',
        ],

        'filter' => [
            'label' => 'Filtrer',
        ],

        'group' => [
            'label' => 'Gruppe',
        ],

        'open_bulk_actions' => [
            'label' => 'Åbn handlinger',
        ],

        'column_manager' => [
            'label' => 'Vælg kolonner',
        ],
    ],

    'empty' => [
        'heading' => 'Ingen resultater',
        'description' => 'Opret en :model for at komme igang.',
    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Anvend filtre',
            ],

            'remove' => [
                'label' => 'Fjern filter',
            ],

            'remove_all' => [
                'label' => 'Fjern alle filtre',
                'tooltip' => 'Fjern alle filtre',
            ],

            'reset' => [
                'label' => 'Nulstil',
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

            'label' => 'Slettede rækker',

            'only_trashed' => 'Kun slettede rækker',

            'with_trashed' => 'Med slettede rækker',

            'without_trashed' => 'Uden slettede rækker',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Gruppere på',
                'placeholder' => 'Gruppere på',
            ],

            'direction' => [

                'label' => 'Grupperingsretning',

                'options' => [
                    'asc' => 'Stigende',
                    'desc' => 'Faldende',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Træk og slip rækkerne i den ønskede rækkefølge.',

    'selection_indicator' => [

        'selected_count' => '1 række valgt|:count rækker valgt',

        'actions' => [

            'select_all' => [
                'label' => 'Vælg alle :count',
            ],

            'deselect_all' => [
                'label' => 'Fravælg alle',
            ],
        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sorter efter',
            ],

            'direction' => [

                'label' => 'Sorteringsretning',

                'options' => [
                    'asc' => 'Stigende',
                    'desc' => 'Faldende',
                ],

            ],

        ],

    ],

];
