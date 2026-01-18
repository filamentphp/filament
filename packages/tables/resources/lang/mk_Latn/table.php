<?php

return [

    'column_manager' => [

        'heading' => 'Koloni',

        'actions' => [

            'apply' => [
                'label' => 'Primeni koloni',
            ],

            'reset' => [
                'label' => 'Resetiraj',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Akcija|Akcii',
        ],

        'select' => [

            'loading_message' => 'Se včituva...',

            'no_options_message' => 'Nema dostapni opcii.',

            'no_search_results_message' => 'Nema opcii koi se sovpaѓaat so vašeto prebaruvanje.',

            'placeholder' => 'Izberi opcija',

            'searching_message' => 'Se prebaruva...',

            'search_prompt' => 'Započnete da pišuvate za prebaruvanje...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Prikaži :count pomalku',
                'expand_list' => 'Prikaži :count povekje',
            ],

            'more_list_items' => 'i :count povekje',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Izberi/odberi site stavki za masovni akcii.',
        ],

        'bulk_select_record' => [
            'label' => 'Izberi/odberi stavka :key za masovni akcii.',
        ],

        'bulk_select_group' => [
            'label' => 'Izberi/odberi grupa :title za masovni akcii.',
        ],

        'search' => [
            'label' => 'Prebaraj',
            'placeholder' => 'Prebaraj',
            'indicator' => 'Prebaraj',
        ],

    ],

    'summary' => [

        'heading' => 'Rezime',

        'subheadings' => [
            'all' => 'Site :label',
            'group' => ':group rezime',
            'page' => 'Ovaa stranica',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Prosek',
            ],

            'count' => [
                'label' => 'Broj',
            ],

            'sum' => [
                'label' => 'Zbir',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Završi preureduvanje na zapisi',
        ],

        'enable_reordering' => [
            'label' => 'Preuredi zapisi',
        ],

        'filter' => [
            'label' => 'Filter',
        ],

        'group' => [
            'label' => 'Grupa',
        ],

        'open_bulk_actions' => [
            'label' => 'Masovni akcii',
        ],

        'column_manager' => [
            'label' => 'Menadžer na koloni',
        ],

    ],

    'empty' => [

        'heading' => 'Nema :model',

        'description' => 'Kreiraj :model za da započneš.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Primeni filtri',
            ],

            'remove' => [
                'label' => 'Otstrani filter',
            ],

            'remove_all' => [
                'label' => 'Otstrani site filtri',
                'tooltip' => 'Otstrani site filtri',
            ],

            'reset' => [
                'label' => 'Resetiraj',
            ],

        ],

        'heading' => 'Filtri',

        'indicator' => 'Aktivni filtri',

        'multi_select' => [
            'placeholder' => 'Site',
        ],

        'select' => [

            'placeholder' => 'Site',

            'relationship' => [
                'empty_option_label' => 'Nema',
            ],

        ],

        'trashed' => [

            'label' => 'Izbrišani zapisi',

            'only_trashed' => 'Samo izbrišani zapisi',

            'with_trashed' => 'So izbrišani zapisi',

            'without_trashed' => 'Bez izbrišani zapisi',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Grupiraj po',
            ],

            'direction' => [

                'label' => 'Nasoka na grupiranje',

                'options' => [
                    'asc' => 'Rastečki',
                    'desc' => 'Opaѓački',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Vleči i spušti gi zapisite po redosled.',

    'selection_indicator' => [

        'selected_count' => '1 zapis izbran|:count zapisi izabrani',

        'actions' => [

            'select_all' => [
                'label' => 'Izberi site :count',
            ],

            'deselect_all' => [
                'label' => 'Odberi site',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sortiraj po',
            ],

            'direction' => [

                'label' => 'Nasoka na sortiranje',

                'options' => [
                    'asc' => 'Rastečki',
                    'desc' => 'Opaѓački',
                ],

            ],

        ],

    ],

    'default_model_label' => 'zapis',

];
