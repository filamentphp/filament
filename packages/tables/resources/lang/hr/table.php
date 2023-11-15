<?php

return [

    'column_toggle' => [

        'heading' => 'Kolone',

    ],

    'columns' => [

        'text' => [
            'more_list_items' => 'i :count još',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Odaberi/poništi odabir svih stavki za skupne radnje.',
        ],

        'bulk_select_record' => [
            'label' => 'Odaberi/poništi odabir stavke :key za skupne radnje.',
        ],

        'bulk_select_group' => [
            'label' => 'Odaberi/poništi odabir grupe :title za skupne radnje.',
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
                'label' => 'Prosijek',
            ],

            'count' => [
                'label' => 'Broj',
            ],

            'sum' => [
                'label' => 'Zbroj',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Završi mijenjanje redolijeda zapisa',
        ],

        'enable_reordering' => [
            'label' => 'Mijenjanje redolijeda zapisa',
        ],

        'filter' => [
            'label' => 'Filter',
        ],

        'group' => [
            'label' => 'Grupa',
        ],

        'open_bulk_actions' => [
            'label' => 'Skupne radnje',
        ],

        'toggle_columns' => [
            'label' => 'Prikaži/sakrij kolone',
        ],

    ],

    'empty' => [

        'heading' => 'Nema :model',

        'description' => 'Stvorite :model kako biste počeli.',

    ],

    'filters' => [

        'actions' => [

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
        ],

        'trashed' => [

            'label' => 'Obrisani zapisi',

            'only_trashed' => 'Samo obrisani zapisi',

            'with_trashed' => 'Sa obrisanih zapisa',

            'without_trashed' => 'Bez obrisanih zapisa',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Grupiraj prema',
                'placeholder' => 'Grupiraj prema',
            ],

            'direction' => [

                'label' => 'Smjer grupiranja',

                'options' => [
                    'asc' => 'Uzlazno',
                    'desc' => 'Silazno',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Povucite i ispustite zapise u redoslijed.',

    'selection_indicator' => [

        'selected_count' => '1 odabrani zapis|:count odabranih zapisa',

        'actions' => [

            'select_all' => [
                'label' => 'Odaberi svih :count',
            ],

            'deselect_all' => [
                'label' => 'Odznači sve',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sortiraj prema',
            ],

            'direction' => [

                'label' => 'Smjer sortiranja',

                'options' => [
                    'asc' => 'Uzlazno',
                    'desc' => 'Silazno',
                ],

            ],

        ],

    ],

];
