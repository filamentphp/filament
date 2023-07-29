<?php

return [

    'columns' => [

        'text' => [
            'more_list_items' => 'na :count zaidi',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Chagua/acha kuchagua vipengee vyote kwa vitendo vingi.',
        ],

        'bulk_select_record' => [
            'label' => 'Chagua/acha kuchagua kipengele :key kwa vitendo vingi.',
        ],

        'search' => [
            'label' => 'Tafuta',
            'placeholder' => 'Tafuta',
        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Maliza kupangilia rekodi upya',
        ],

        'enable_reordering' => [
            'label' => 'Pangilia rekodi',
        ],

        'filter' => [
            'label' => 'Chuja',
        ],

        'open_bulk_actions' => [
            'label' => 'Fungua matendo',
        ],

        'toggle_columns' => [
            'label' => 'Geuza safu',
        ],

    ],

    'empty' => [
        'heading' => 'Hakuna rekodi zilizopatikana',
    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => 'Toa mchujo',
            ],

            'remove_all' => [
                'label' => 'Toa michujo yote',
                'tooltip' => 'Toa michujo yote',
            ],

            'reset' => [
                'label' => 'Weka upya michujo',
            ],

        ],

        'indicator' => 'Michujo inayotumika',

        'multi_select' => [
            'placeholder' => 'Zote',
        ],

        'select' => [
            'placeholder' => 'Zote',
        ],

        'trashed' => [

            'label' => 'Rekodi zilizofutwa',

            'only_trashed' => 'Rekodi zilizofutwa pekee',

            'with_trashed' => 'Pamoja na rekodi zilizofutwa',

            'without_trashed' => 'Bila rekodi zilizofutwa',

        ],

    ],

    'reorder_indicator' => 'Buruta na uangushe rekodi kwa mpangilio.',

    'selection_indicator' => [

        'selected_count' => 'Rekodi 1 imeshaguliwa|Rekodi :count zimeshaguliwa',

        'actions' => [

            'select_all' => [
                'label' => 'Chagua :count',
            ],

            'deselect_all' => [
                'label' => 'Acha kuchagua zote',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Panga kwa',
            ],

            'direction' => [

                'label' => 'Panga mwelekeo',

                'options' => [
                    'asc' => 'Kupanda',
                    'desc' => 'Kushuka',
                ],

            ],

        ],

    ],

];
