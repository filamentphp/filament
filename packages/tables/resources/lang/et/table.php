<?php

return [

    'column_toggle' => [

        'heading' => 'Veerud',

    ],

    'columns' => [

        'actions' => [
            'label' => 'Tegevus|Tegevused',
        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Näita :count vähem',
                'expand_list' => 'Näita :count rohkem',
            ],

            'more_list_items' => 'ja veel :count',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Vali/tühista kõik kirjed hulgitoiminguteks.',
        ],

        'bulk_select_record' => [
            'label' => 'Vali/tühista kirje :key hulgitoiminguteks.',
        ],

        'bulk_select_group' => [
            'label' => 'Vali/tühista grupp :title hulgitoiminguteks.',
        ],

        'search' => [
            'label' => 'Otsing',
            'placeholder' => 'Otsi',
            'indicator' => 'Otsi',
        ],

    ],

    'summary' => [

        'heading' => 'Kokkuvõte',

        'subheadings' => [
            'all' => 'Kõik :label',
            'group' => ':group kokkuvõte',
            'page' => 'See leht',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Keskmine',
            ],

            'count' => [
                'label' => 'Arv',
            ],

            'sum' => [
                'label' => 'Summa',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Lõpeta kirjete ümberjärjestamine',
        ],

        'enable_reordering' => [
            'label' => 'Järjesta kirjed ümber',
        ],

        'filter' => [
            'label' => 'Filtreeri',
        ],

        'group' => [
            'label' => 'Grupeeri',
        ],

        'open_bulk_actions' => [
            'label' => 'Hulgitoimingud',
        ],

        'toggle_columns' => [
            'label' => 'Lülita veerge',
        ],

    ],

    'empty' => [

        'heading' => ':model puudub',

        'description' => 'Loo :model alustamiseks.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Rakenda filtrid',
            ],

            'remove' => [
                'label' => 'Eemalda filter',
            ],

            'remove_all' => [
                'label' => 'Eemalda kõik filtrid',
                'tooltip' => 'Eemalda kõik filtrid',
            ],

            'reset' => [
                'label' => 'Lähtesta',
            ],

        ],

        'heading' => 'Filtrid',

        'indicator' => 'Aktiivsed filtrid',

        'multi_select' => [
            'placeholder' => 'Kõik',
        ],

        'select' => [
            'placeholder' => 'Kõik',
        ],

        'trashed' => [

            'label' => 'Kustutatud kirjed',

            'only_trashed' => 'Ainult kustutatud kirjed',

            'with_trashed' => 'Koos kustutatud kirjetega',

            'without_trashed' => 'Ilma kustutatud kirjeteta',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Grupeeri',
                'placeholder' => 'Grupeeri',
            ],

            'direction' => [

                'label' => 'Grupeerimise suund',

                'options' => [
                    'asc' => 'Kasvav',
                    'desc' => 'Kahanev',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Lohista kirjed õigesse järjekorda.',

    'selection_indicator' => [

        'selected_count' => '1 kirje valitud|:count kirjet valitud',

        'actions' => [

            'select_all' => [
                'label' => 'Vali kõik :count',
            ],

            'deselect_all' => [
                'label' => 'Tühista kõik valikud',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sorteeri',
            ],

            'direction' => [

                'label' => 'Sorteerimise suund',

                'options' => [
                    'asc' => 'Kasvav',
                    'desc' => 'Kahanev',
                ],

            ],

        ],

    ],

];
