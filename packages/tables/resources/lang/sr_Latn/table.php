<?php

return [

    'column_manager' => [

        'heading' => 'Kolone',

        'actions' => [

            'apply' => [
                'label' => 'Primeni kolone',
            ],

            'reset' => [
                'label' => 'Poništi',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Akcija|Akcije',
        ],

        'select' => [

            'loading_message' => 'Učitavanje...',

            'no_search_results_message' => 'Nijedna opcija ne se podudara.',

            'placeholder' => 'Izaberite opciju',

            'searching_message' => 'Pretraga...',

            'search_prompt' => 'Započnite unos za pretragu...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Prikaži :count manje',
                'expand_list' => 'Prikaži :count više',
            ],

            'more_list_items' => 'i još :count',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Izaberi/poništi izbor svih stavki za grupne radnje.',
        ],

        'bulk_select_record' => [
            'label' => 'Izaberi/poništi izabrane stavke :key za grupne radnje.',
        ],

        'bulk_select_group' => [
            'label' => 'Izaberi/poništi izabrane grupe :title za grupne radnje.',
        ],

        'search' => [
            'label' => 'Pretraga',
            'placeholder' => 'Pretraži',
            'indicator' => 'Pretraži',
        ],

    ],

    'summary' => [

        'heading' => 'Sažetak',

        'subheadings' => [
            'all' => 'Sve :label',
            'group' => ':group sažetak',
            'page' => 'Ova stranica',
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
            'label' => 'Onemogući promenu redosleda zapisa',
        ],

        'enable_reordering' => [
            'label' => 'Promena redosleda zapisa',
        ],

        'filter' => [
            'label' => 'Filter',
        ],

        'group' => [
            'label' => 'Grupa',
        ],

        'open_bulk_actions' => [
            'label' => 'Grupne radnje',
        ],

        'column_manager' => [
            'label' => 'Prikaži/sakrij kolone',
        ],

    ],

    'empty' => [

        'heading' => 'Nema :model',

        'description' => 'Stvorite :model da biste počeli.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Primeni filter',
            ],

            'remove' => [
                'label' => 'Ukloni filter',
            ],

            'remove_all' => [
                'label' => 'Ukloni sve filtere',
                'tooltip' => 'Ukloni sve filtere',
            ],

            'reset' => [
                'label' => 'Poništi',
            ],

        ],

        'heading' => 'Filteri',

        'indicator' => 'Aktivni filteri',

        'multi_select' => [
            'placeholder' => 'Sve',
        ],

        'select' => [
            'placeholder' => 'Sve',

            'relationship' => [
                'empty_option_label' => 'Ništa',
            ],
        ],

        'trashed' => [

            'label' => 'Izbrisani zapisi',

            'only_trashed' => 'Samo izbrisani zapisi',

            'with_trashed' => 'Sa izbrisanim zapisima',

            'without_trashed' => 'Bez izbrisanih zapisa',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Grupišite po',
            ],

            'direction' => [

                'label' => 'Smer grupisanja',

                'options' => [
                    'asc' => 'Uzlazno',
                    'desc' => 'Silazno',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Povuci i ispusti zapise u redosledu.',

    'selection_indicator' => [

        'selected_count' => '1 izabrani zapis|:count izabranih zapisa',

        'actions' => [

            'select_all' => [
                'label' => 'Izaberite sve :count',
            ],

            'deselect_all' => [
                'label' => 'Označite sve',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sortiraj po',
            ],

            'direction' => [

                'label' => 'Smer sortiranja',

                'options' => [
                    'asc' => 'Uzlazno',
                    'desc' => 'Silazno',
                ],

            ],

        ],

    ],

    'default_model_label' => 'zapis',

];
