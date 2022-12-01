<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'un :count vēl',
        ],

        'messages' => [
            'copied' => 'Kopēts',
        ],

    ],

    'fields' => [

        'search_query' => [
            'label' => 'Meklēt',
            'placeholder' => 'Meklēt',
        ],

    ],

    'pagination' => [

        'label' => 'Lapdales navigācija',

        'overview' => 'Rāda :first līdz :last no :total rezultātiem',

        'fields' => [

            'records_per_page' => [

                'label' => 'vienā lappusē',

                'options' => [
                    'all' => 'Visi',
                ],

            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Doties uz lapu :page',
            ],

            'next' => [
                'label' => 'Nākamais',
            ],

            'previous' => [
                'label' => 'Iepriekšējais',
            ],

        ],

    ],

    'buttons' => [

        'disable_reordering' => [
            'label' => 'Pabeigt ierakstu kārtošanu',
        ],

        'enable_reordering' => [
            'label' => 'Kārtot ierakstus',
        ],

        'filter' => [
            'label' => 'Filtrēt',
        ],

        'open_actions' => [
            'label' => 'Atvērt darbības',
        ],

        'toggle_columns' => [
            'label' => 'Izvēlēties kolonnas',
        ],

    ],

    'empty' => [
        'heading' => 'Nav atrasts neviens ieraksts',
    ],

    'filters' => [

        'buttons' => [

            'remove' => [
                'label' => 'Noņemt filtru',
            ],

            'remove_all' => [
                'label' => 'Noņemt visus filtrus',
                'tooltip' => 'Noņemt visus filtrus',
            ],

            'reset' => [
                'label' => 'Atiestatīt filtrus',
            ],

        ],

        'indicator' => 'Aktīvie filtri',

        'multi_select' => [
            'placeholder' => 'Visi',
        ],

        'select' => [
            'placeholder' => 'Visi',
        ],

        'trashed' => [

            'label' => 'Dzēstie ieraksti',

            'only_trashed' => 'Tikai dzēstie ieraksti',

            'with_trashed' => 'Kopā ar dzēstajiem ierakstiem',

            'without_trashed' => 'Bez dzēstajiem ierakstiem',

        ],

    ],

    'reorder_indicator' => 'Velciet un nometiet ierakstus secībā.',

    'selection_indicator' => [

        'selected_count' => 'Izvēlēts 1 ieraksts.|:count ieraksti izvēlēti.',

        'buttons' => [

            'select_all' => [
                'label' => 'Atlasīt visus :count',
            ],

            'deselect_all' => [
                'label' => 'Noņemt atlasi visiem',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Kārtot pēc',
            ],

            'direction' => [

                'label' => 'Kārtošanas virziens',

                'options' => [
                    'asc' => 'Augošs',
                    'desc' => 'Dilstošs',
                ],

            ],

        ],

    ],

];
