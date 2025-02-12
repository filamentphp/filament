<?php

return [

    'column_toggle' => [

        'heading' => 'Kolomme',

    ],

    'columns' => [

        'actions' => [
            'label' => 'Aksie|Aksies',
        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Wys :count minder',
                'expand_list' => 'Show :count meer',
            ],

            'more_list_items' => 'en :count meer',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Kies/ontkies alle items vir grootmaataksies.',
        ],

        'bulk_select_record' => [
            'label' => 'Kies/ontkies item :key vir grootmaat aksies.',
        ],

        'bulk_select_group' => [
            'label' => 'Kies/ontkies groep :title vir grootmaataksies.',
        ],

        'search' => [
            'label' => 'Soek',
            'placeholder' => 'Soek',
            'indicator' => 'Soek',
        ],

    ],

    'summary' => [

        'heading' => 'Opsomming',

        'subheadings' => [
            'all' => 'Almal :label',
            'group' => ':group opsomming',
            'page' => 'Hierdie bladsy',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Gemiddeld',
            ],

            'count' => [
                'label' => 'Tel',
            ],

            'sum' => [
                'label' => 'Som',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Voltooi die herrangskikking van rekords',
        ],

        'enable_reordering' => [
            'label' => 'Herrangskik rekords',
        ],

        'filter' => [
            'label' => 'Filter',
        ],

        'group' => [
            'label' => 'Groep',
        ],

        'open_bulk_actions' => [
            'label' => 'Grootmaat aksies',
        ],

        'toggle_columns' => [
            'label' => 'Wissel kolomme',
        ],

    ],

    'empty' => [

        'heading' => 'Nee :model',

        'description' => 'Skep \'n :model om te begin.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Pas filters toe',
            ],

            'remove' => [
                'label' => 'Verwyder filter',
            ],

            'remove_all' => [
                'label' => 'Verwyder alle filters',
                'tooltip' => 'Verwyder alle filters',
            ],

            'reset' => [
                'label' => 'Stel terug',
            ],

        ],

        'heading' => 'Filters',

        'indicator' => 'Aktiewe filters',

        'multi_select' => [
            'placeholder' => 'Almal',
        ],

        'select' => [
            'placeholder' => 'Almal',
        ],

        'trashed' => [

            'label' => 'Geskrap rekords',

            'only_trashed' => 'Slegs geskrap rekords',

            'with_trashed' => 'Met geskrap rekords',

            'without_trashed' => 'Sonder geskrap rekords',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Groepeer deur',
                'placeholder' => 'Groepeer deur',
            ],

            'direction' => [

                'label' => 'Groeprigting',

                'options' => [
                    'asc' => 'Stygend',
                    'desc' => 'Dalend',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Sleep en los die rekords in volgorde.',

    'selection_indicator' => [

        'selected_count' => '1 rekord gekies|:count rekords gekies',

        'actions' => [

            'select_all' => [
                'label' => 'Kies alles :count',
            ],

            'deselect_all' => [
                'label' => 'Ontkies almal',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sorteer volgens',
            ],

            'direction' => [

                'label' => 'Sorteer rigting',

                'options' => [
                    'asc' => 'Stygend',
                    'desc' => 'Dalend',
                ],

            ],

        ],

    ],

];
