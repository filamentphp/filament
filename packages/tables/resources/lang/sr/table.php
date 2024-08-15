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
            'label' => 'Odaberi/poništi odabir svih stavki za grupne akcije.',
        ],

        'bulk_select_record' => [
            'label' => 'Odaberi/poništi odabir stavke :key za grupne akcije.',
        ],

        'bulk_select_group' => [
            'label' => 'Odaberi/poništi odabir grupe :title za grupne akcije.',
        ],

        'search' => [
            'label' => 'Pretraga',
            'placeholder' => 'Pretraži',
            'indicator' => 'Pretraži',
        ],

    ],

    'summary' => [

        'heading' => 'Rezime',

        'subheadings' => [
            'all' => 'Sve :label',
            'group' => ':group rezime',
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
                'label' => 'Suma',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Onemogući preuređivanje',
        ],

        'enable_reordering' => [
            'label' => 'Omogući preuređivanje',
        ],

        'filter' => [
            'label' => 'Filter',
        ],

        'group' => [
            'label' => 'Grupa',
        ],

        'open_bulk_actions' => [
            'label' => 'Grupne akcije',
        ],

        'toggle_columns' => [
            'label' => 'Prikaži/sakrij kolone',
        ],

    ],

    'empty' => [

        'heading' => 'Nema :model',

        'description' => 'Napravite :model kako biste počeli.',

    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => 'Odstrani filter',
            ],

            'remove_all' => [
                'label' => 'Odstrani sve filtere',
                'tooltip' => 'Odstrani sve filtere',
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

            'label' => 'Obrisani redovi',

            'only_trashed' => 'Samo obrisani redovi',

            'with_trashed' => 'Sa obrisanim redovima',

            'without_trashed' => 'Bez obrisanih redova',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Grupiši prema',
                'placeholder' => 'Grupiši prema',
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

    'reorder_indicator' => 'Povucite i spustite red u redosled.',

    'selection_indicator' => [

        'selected_count' => '1 odabrani red|:count odabranih redova',

        'actions' => [

            'select_all' => [
                'label' => 'Odaberi svih :count',
            ],

            'deselect_all' => [
                'label' => 'Poništiti sve',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sortiraj prema',
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

];
