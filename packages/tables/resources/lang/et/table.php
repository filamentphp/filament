<?php

return [

    'column_manager' => [

        'heading' => 'Veerud',

        'actions' => [

            'apply' => [
                'label' => 'Rakenda',
            ],

            'reset' => [
                'label' => 'Lähtesta',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Tegevus|Tegevused',
        ],

        'select' => [

            'loading_message' => 'Laadimine...',

            'no_options_message' => 'Valikud puuduvad',

            'no_search_results_message' => 'Ei leitud ühtegi kirjet, mis vastaks teie otsingule.',

            'placeholder' => 'Tee valik',

            'searching_message' => 'Otsimine...',

            'search_prompt' => 'Otsimiseks alustage kirjutamist...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Näita :count vähem',
                'expand_list' => 'Näita :count rohkem',
            ],

            'more_list_items' => 'ja :count rohkem',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Vali/tühista kõik elemendid masstoimingute jaoks.',
        ],

        'bulk_select_record' => [
            'label' => 'Vali/tühista element :key masstoimingute jaoks.',
        ],

        'bulk_select_group' => [
            'label' => 'Vali/tühista rühm :title masstoimingute jaoks.',
        ],

        'search' => [
            'label' => 'Otsi',
            'placeholder' => 'Otsi',
            'indicator' => 'Otsing',
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
            'label' => 'Lõpeta kirjete reastamine',
        ],

        'enable_reordering' => [
            'label' => 'Reasta kirjed',
        ],

        'filter' => [
            'label' => 'Filtreeri',
        ],

        'group' => [
            'label' => 'Rühmita',
        ],

        'open_bulk_actions' => [
            'label' => 'Masstoimingud',
        ],

        'column_manager' => [
            'label' => 'Veeruhaldur',
        ],

    ],

    'empty' => [

        'heading' => 'Kirjeid ei leitud',

        'description' => 'Alustamiseks looge uus kirje.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Rakenda',
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

            'relationship' => [
                'empty_option_label' => 'Ühtegi',
            ],

        ],

        'trashed' => [

            'label' => 'Kustutatud kirjed',

            'only_trashed' => 'Ainult kustutatud kirjed',

            'with_trashed' => 'Kustutatud kirjetega',

            'without_trashed' => 'Ilma kustutatud kirjeteta',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Rühmita',
            ],

            'direction' => [

                'label' => 'Rühmitamise suund',

                'options' => [
                    'asc' => 'Kasvav',
                    'desc' => 'Kahanev',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Lohista kirjed järjekorda.',

    'selection_indicator' => [

        'selected_count' => '1 kirje valitud|:count kirjet valitud',

        'actions' => [

            'select_all' => [
                'label' => 'Vali kõik :count',
            ],

            'deselect_all' => [
                'label' => 'Tühista kõik',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sorteeri järgi',
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

    'default_model_label' => 'kirje',

];
