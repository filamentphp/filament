<?php

return [

    'column_toggle' => [

        'heading' => 'Kolonnas',

    ],

    'columns' => [

        'text' => [
            'more_list_items' => 'un :count vēl',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Atlasīt/noņemt atlasi no visiem ierakstiem, lai veiktu lielapjoma darbības.',
        ],

        'bulk_select_record' => [
            'label' => 'Atlasīt/noņemt atlasi no ierksta :key, lai veiktu lielapjoma darbības.',
        ],

        'bulk_select_group' => [
            'label' => 'Atlasīt/noņemt atlasi no grupas :title, lai veiktu lielapjoma darbības.',
        ],

        'search' => [
            'label' => 'Meklēt',
            'placeholder' => 'Meklēt',
            'indicator' => 'Meklēt',
        ],

    ],

    'summary' => [

        'heading' => 'Kopsavilkums',

        'subheadings' => [
            'all' => 'Visi :label',
            'group' => ':group kopsavilkums',
            'page' => 'Šī lapa',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Vidēji',
            ],

            'count' => [
                'label' => 'Skaits',
            ],

            'sum' => [
                'label' => 'Summa',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Pabeigt ierakstu kārtošanu',
        ],

        'enable_reordering' => [
            'label' => 'Kārtot ierakstus',
        ],

        'filter' => [
            'label' => 'Filtrēt',
        ],

        'group' => [
            'label' => 'Grupēt',
        ],

        'open_bulk_actions' => [
            'label' => 'Lielapjoma darbības',
        ],

        'toggle_columns' => [
            'label' => 'Izvēlēties kolonnas',
        ],

    ],

    'empty' => [

        'heading' => 'Nav atrasts neviens ieraksts',

        'description' => 'Izveidot :model, lai sāktu.',

    ],

    'filters' => [

        'actions' => [

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

        'heading' => 'Filtri',

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

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Grupēt pēc',
                'placeholder' => 'Grupēt pēc',
            ],

            'direction' => [

                'label' => 'Grupēšanas virziens',

                'options' => [
                    'asc' => 'Augošs',
                    'desc' => 'Dilstošs',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Velciet un nometiet ierakstus secībā.',

    'selection_indicator' => [

        'selected_count' => 'Izvēlēts 1 ieraksts|:count ieraksti izvēlēti',

        'actions' => [

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
